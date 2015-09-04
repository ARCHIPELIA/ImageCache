<?php

namespace Atoll\ImageCache;

use Atoll\ImageCache\Exception\RuntimeException;
use Imagine\Image\ImageInterface;

class Image
{
  /**
   * @var ImageInterface
   */
  protected $image;
  /**
   * @var string
   */
  protected $baseUrl;
  /**
   * @var int
   */
  public $x;
  /**
   * @var int
   */
  public $y;

  /**
   * @param ImageInterface $image
   */
  public function __construct(ImageInterface $image)
  {
    $this->image = $image;
    $this->x = $image->getSize()->getWidth();
    $this->y = $image->getSize()->getHeight();
  }

  /**
   * @return string
   */
  public function getBaseUrl()
  {
    return $this->baseUrl;
  }

  /**
   * @param $baseUrl string
   *
   * @return Image
   */
  public function setBaseUrl($baseUrl)
  {
    $this->baseUrl = $baseUrl;
    return $this;
  }

  /**
   * @return string
   */
  public function getImagePath()
  {
    $metadata = $this->image->metadata();
    return $metadata['filepath'];
  }

  /**
   * @return string
   */
  public function getImageUrl()
  {
    $metadata = $this->image->metadata();
    return $this->baseUrl . '/' . basename($metadata['uri']);
  }

  /**
   * @return mixed
   * @throws RuntimeException
   */
  public function getMimeType()
  {
    $imageInfos = @getimagesize($this->getImagePath());

    if ($imageInfos === false) {
      throw new RuntimeException(sprintf('Failed to open file %s', $this->getImagePath()));
    }

    return $imageInfos['mime'];
  }
}