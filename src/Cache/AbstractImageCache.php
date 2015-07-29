<?php

namespace Atoll\ImageCache\Cache;

class AbstractImageCache
{
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
   * {@inheritdoc}
   */
  public function getBasePath()
  {
    return $this->basePath;
  }

  /**
   * {@inheritdoc}
   */
  public function setBasePath($basePath)
  {
    $this->basePath = $basePath;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTmpPath()
  {
    return $this->tmpPath;
  }

  /**
   * {@inheritdoc}
   */
  public function setTmpPath($tmpPath)
  {
    $this->tmpPath = $tmpPath;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseUrl()
  {
    return $this->baseUrl;
  }

  /**
   * {@inheritdoc}
   */
  public function setBaseUrl($baseUrl)
  {
    $this->baseUrl = $baseUrl;
    return $this;
  }
}