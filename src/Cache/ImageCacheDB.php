<?php

namespace Atoll\ImageCache\Cache;

use Atoll\ImageCache\Category\ImageCategoryDB;
use Atoll\ImageCache\Category\ImageCategoryInterface;
use Atoll\ImageCache\Exception\RuntimeException;
use Atoll\ImageCache\Image;
use Atoll\ImageCache\ImageManager;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class ImageCacheDB extends AbstractImageCache implements ImageCacheInterface
{
  /**
   * @var mixed
   */
  protected $BdD;

  /**
   * @param $DB
   */
  public function __construct($DB)
  {
    $this->BdD = $DB;
  }

  /**
   * {@inheritdoc}
   * @throws RuntimeException
   */
  public function getImage(ImageCategoryInterface $category, $size, $id)
  {
    if (!($category instanceof ImageCategoryDB)) {
      throw new RuntimeException('Image cache DB only accept ImageCategoryDB');
    }

    try {
      $originalPath = $this->getOriginalPath($category, $id);
      $image = (new Imagine())
        ->open($originalPath);
    } catch (InvalidArgumentException $e) {
      $image = $this->generateOriginalImage($category, $id);
    }

    if ($size === ImageManager::ORIGINAL_SIZE_NAME) {
      $originalUrl  = $this->getOriginalUrl($category, $id);
      return (new Image($image))
        ->setBaseUrl($originalUrl);
    }

    try {
      $resizePath = $this->getPath($category, $size, $id);
      $resizeImage = (new Imagine())
        ->open($resizePath);
    } catch (InvalidArgumentException $e) {
      try {
        $resizeImage = $this->generateImage($image, $category, $size, $id);
      } catch (\Exception $e) {
        throw new RuntimeException($e->getMessage());
      }
    }

    $resizeUrl  = $this->getUrl($category, $size, $id);
    return (new Image($resizeImage))
      ->setBaseUrl($resizeUrl);
  }

  /**
   * {@inheritdoc}
   * @throws RuntimeException
   */
  public function generateCache(ImageCategoryInterface $category, $id)
  {
    if (!($category instanceof ImageCategoryDB)) {
      throw new RuntimeException('Image cache DB only accept ImageCategoryDB');
    }

    $sizes = $category->getAvailableSize();
    foreach ($sizes as $size) {
      $this->getImage($category, $size, $id);
    }

    return true;
  }

  /**
   * {@inheritdoc}
   * @throws RuntimeException
   */
  public function deleteCache(ImageCategoryInterface $category, $id)
  {
    if (!($category instanceof ImageCategoryDB)) {
      throw new RuntimeException('Image cache DB only accept ImageCategoryDB');
    }

    $globPath = $this->getPath($category, '*', $id);

    if (!($paths = @glob($globPath))) {
      throw new RuntimeException(sprintf('Error while deleting cache (%s:%s) : glob() error', $category->getName(), $id));
    }

    foreach ($paths as $path) {
      if (!@unlink($path)) {
        throw new RuntimeException(sprintf('Error while deleting cache (%s:%s) : unlink() error', $category->getName(), $id));
      }
    }

    return true;
  }

  /**
   * @param ImageCategoryInterface $category
   * @param string|array $size
   * @param int $id
   * @return string
   */
  public function getPath(ImageCategoryInterface $category, $size, $id)
  {
    $basePart = (strpos($this->basePath, '/') !== 0) ? dirname(__FILE__) . $this->basePath : $this->basePath;

    $subPart = $category->getSubPath();

    $sizePart = $size;
    if (is_array($size)) {
      $sizePart = sprintf('[%dx%d]', $size['x'], $size['y']);
    }

    $filePart = $category->getImageName($id);

    return sprintf('%s/%s/%s/%s', rtrim($basePart, '/'), rtrim($subPart, '/'), rtrim($sizePart, '/'), $filePart);
  }

  /**
   * @param ImageCategoryInterface $category
   * @param int $id
   * @return string
   */
  protected function getOriginalPath(ImageCategoryInterface $category, $id)
  {
    return $this->getPath($category, 'ORIGINAL', $id);
  }

  /**
   * @param ImageCategoryInterface $category
   * @param string|array $size
   * @param int $id
   * @return string
   */
  protected function getUrl(ImageCategoryInterface $category, $size, $id)
  {
    $basePart = (!isset($this->baseUrl)) ? sprintf('%s://%s', (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https' : 'http', $_SERVER['HTTP_HOST']) : $this->baseUrl;

    $subPart = $category->getSubPath();

    $sizePart = $size;
    if (is_array($size)) {
      $sizePart = sprintf('[%dx%d]', $size['x'], $size['y']);
    }

    return sprintf('%s/%s/%s', rtrim($basePart, '/'), rtrim($subPart, '/'), rtrim($sizePart, '/'));
  }

  /**
   * @param ImageCategoryInterface $category
   * @param int $id
   * @return string
   */
  protected function getOriginalUrl(ImageCategoryInterface $category, $id)
  {
    return $this->getUrl($category, 'ORIGINAL', $id);
  }

  /**
   * @param ImageInterface $image
   * @param ImageCategoryInterface $category
   * @param array $size
   * @param int $id
   * @throws RuntimeException
   * @return ImageInterface
   */
  protected function generateImage(ImageInterface $image, ImageCategoryInterface $category, $size, $id)
  {
    $resizePath = $this->getPath($category, $size, $id);
    if (!file_exists(dirname($resizePath))) {
      if (!@mkdir(dirname($resizePath), 0755, true)) {
        throw new RuntimeException(sprintf("Unable to create the folder tree '%s'", $resizePath));
      }
    }

    $metadata = $image->metadata();
    switch (exif_imagetype($metadata['filepath'])) {
      case IMAGETYPE_JPEG:
        $format = 'jpeg';
        break;
      case IMAGETYPE_GIF:
        $format = 'gif';
        break;
      case IMAGETYPE_PNG:
        $format = 'png';
        break;
      case IMAGETYPE_XBM:
        $format = 'xbm';
        break;
      case IMAGETYPE_WBMP:
        $format = 'wbmp';
        break;
      default:
        $format = false;
    }

    // Ratio
    $rat_x  =  $image->getSize()->getWidth() / $size['x'];
    $rat_y  =  $image->getSize()->getHeight() / $size['y'];

    // Size
    $dest_x = $image->getSize()->getWidth();
    $dest_y = $image->getSize()->getHeight();
    if ($rat_x > 1 || $rat_y > 1){
      if ($rat_x > $rat_y){
        $dest_x  =  $size['x'];
        $dest_y  =  $image->getSize()->getHeight() / $rat_x;
      } else {
        $dest_x  =  $image->getSize()->getWidth() / $rat_y;
        $dest_y  =  $size['y'];
      }
    }

    return $image->resize(new Box($dest_x, $dest_y))
                 ->save($resizePath, ['format' => $format]);
  }

  /**
   * @param ImageCategoryDB $category
   * @param int $id
   * @throws RuntimeException
   * @return ImageInterface
   */
  protected function generateOriginalImage(ImageCategoryDB $category, $id)
  {
    $originalPath = $this->getOriginalPath($category, $id);
    if (!file_exists(dirname($originalPath))) {
      if (!@mkdir(dirname($originalPath), 0755, true)) {
        throw new \RuntimeException(sprintf("Unable to create the folder tree '%s'", $originalPath));
      }
    }

    $this->BdD->SqlBindByName(':img_id', $id);
    $blob = $this->BdD->SqlFetchField($category->getContentQuery());

    if (!is_a($blob, 'OCI-Lob')) {
      throw new RuntimeException("Invalid image field, OCI-Lob expected!");
    }

    if (!@file_put_contents($originalPath, $blob->load())) {
      unlink($originalPath);
      throw new RuntimeException(sprintf("Error while writing image '%s'", $originalPath));
    }

    return (new Imagine())
            ->open($originalPath);
  }
}