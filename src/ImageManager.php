<?php

namespace Atoll\ImageCache;

use Atoll\ImageCache\Cache\ImageCacheInterface;
use Atoll\ImageCache\Category\ImageCategoryInterface;
use Atoll\ImageCache\Exception\InvalidArgumentException;

class ImageManager
{
  const ORIGINAL_SIZE_NAME  = 'ORIGINAL';
  const MIME_SUPPORTED      =  ['image/gif', 'image/jpeg', 'image/png', 'image/bmp'];

  /**
   * @var ImageCacheInterface
   */
  protected $cache;
  /**
   * @var string
   */
  protected $basePath;
  /**
   * @var string
   */
  protected $tmpPath;
  /**
   * @var string
   */
  protected $baseUrl;
  /**
   * @var array
   */
  protected $sizes      = array();
  /**
   * @var array
   */
  protected $categories = array();

  /**
   * @param ImageCacheInterface $cache
   * @param string $basePath
   * @param string $tmpPath
   */
  public function __construct(ImageCacheInterface $cache, $basePath, $tmpPath)
  {
    $this->cache    = $cache;
    $this->basePath = $basePath;
    $this->tmpPath  = $tmpPath;

    $cache->setBasePath($basePath)
          ->setTmpPath($tmpPath);
  }

  /**
   * @return string
   */
  public function getBasePath()
  {
    return $this->basePath;
  }

  /**
   * @param string $basePath
   * @return ImageManager $this
   */
  public function setBasePath($basePath)
  {
    $this->basePath = $basePath;

    if ($this->cache !== null) {
      $this->cache->setBasePath($basePath);
    }

    return $this;
  }

  /**
   * @return string
   */
  public function getTmpPath()
  {
    return $this->tmpPath;
  }

  /**
   * @param string $tmpPath
   * @return ImageManager $this
   */
  public function setTmpPath($tmpPath)
  {
    $this->tmpPath = $tmpPath;

    return $this;
  }

  /**
   * @return string
   */
  public function getBaseUrl()
  {
    return $this->baseUrl;
  }

  /**
   * @param string $baseUrl
   * @return ImageManager $this
   */
  public function setBaseUrl($baseUrl)
  {
    $this->baseUrl = $baseUrl;

    if ($this->cache !== null) {
      $this->cache->setBaseUrl($baseUrl);
    }

    return $this;
  }

  /**
   * @param string $name
   * @param array $size
   * @return ImageManager $this
   */
  public function addSize($name, $size)
  {
    $this->sizes[$name] = $size;

    return $this;
  }

  /**
   * @param array $sizes
   * @return ImageManager $this
   */
  public function addSizes(array $sizes)
  {
    foreach ($sizes as $sizeName => $size) {
      $this->addSize($sizeName, $size);
    }

    return $this;
  }

  /**
   * @param string $name
   * @throws InvalidArgumentException
   * @return string|array
   */
  public function getSize($name)
  {
    if ($name === self::ORIGINAL_SIZE_NAME) {
      return $name;
    }

    if (!array_key_exists($name, $this->sizes)) {
      throw new \InvalidArgumentException(sprintf("Image size <b>%s</b> undefined!", $name));
    }

    return $this->sizes[$name];
  }

  /**
   * @param ImageCategoryInterface $category
   * @return ImageManager $this
   */
  public function addCategory(ImageCategoryInterface $category)
  {
    $this->categories[$category->getName()] = $category;

    return $this;
  }

  /**
   * @param array $categories
   * @return ImageManager $this
   */
  public function addCategories(array $categories)
  {
    foreach ($categories as $category) {
      $this->addCategory($category);
    }

    return $this;
  }

  /**
   * @param string $name
   * @throws InvalidArgumentException
   * @return ImageCategoryInterface
   */
  public function getCategory($name)
  {
    if (!array_key_exists($name, $this->categories)) {
      throw new \InvalidArgumentException("Image category <b>%s</b> undefined!", $name);
    }

    return $this->categories[$name];
  }

  /**
   * @param string $categName
   * @param string $sizeName
   * @param mixed $prop
   * @return Image
   */
  public function getImage($categName, $sizeName, $prop)
  {
    $category = $this->getCategory($categName);
    $size     = $this->getSize($sizeName);

    return $this->cache->getImage($category, $size, $prop);
  }

  /**
   * @param string $categName
   * @param mixed $prop
   * @return boolean
   */
  public function generateCache($categName, $prop = null)
  {
    $category = $this->getCategory($categName);

    return $this->cache->generateCache($category, $prop);
  }

  /**
   * @param string $categName
   * @param mixed $prop
   * @return boolean
   */
  public function deleteCache($categName, $prop = null)
  {
    $category = $this->getCategory($categName);

    return $this->cache->deleteCache($category, $prop);
  }

  /**
   * @param string $mime
   * @return boolean
   */
  public function isImageSupported($mime)
  {
    return in_array($mime, self::MIME_SUPPORTED);
  }
}