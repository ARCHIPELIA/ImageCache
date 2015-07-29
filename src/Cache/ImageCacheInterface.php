<?php

namespace Atoll\ImageCache\Cache;

use Atoll\ImageCache\Category\ImageCategoryInterface;
use Atoll\ImageCache\Image;

interface ImageCacheInterface
{
  /**
   * @param ImageCategoryInterface $category
   * @param string $size
   * @param mixed $prop
   * @return Image
   */
  public function getImage(ImageCategoryInterface $category, $size, $prop);

  /**
   * @param ImageCategoryInterface $category
   * @param mixed $prop
   * @return boolean
   */
  public function generateCache(ImageCategoryInterface $category, $prop);

  /**
   * @param ImageCategoryInterface $category
   * @param mixed $prop
   * @return boolean mixed
   */
  public function deleteCache(ImageCategoryInterface $category, $prop);

  /**
   * @return string
   */
  public function getBasePath();

  /**
   * @param string $basePath
   * @return ImageCacheInterface
   */
  public function setBasePath($basePath);

  /**
   * @return string
   */
  public function getTmpPath();

  /**
   * @param string $tmpPath
   * @return ImageCacheInterface
   */
  public function setTmpPath($tmpPath);

  /**
   * @return string
   */
  public function getBaseUrl();

  /**
   * @param string $baseUrl
   * @return ImageCacheInterface
   */
  public function setBaseUrl($baseUrl);
}