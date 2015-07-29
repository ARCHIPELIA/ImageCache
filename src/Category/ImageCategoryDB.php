<?php

namespace Atoll\ImageCache\Category;

class ImageCategoryDB extends AbstractImageCategory implements ImageCategoryInterface
{
  /**
   * @var string
   */
  protected $contentQuery;
  /**
   * @var array
   */
  protected $availableSize  = array();
  /**
   * @var string
   */
  protected $subPath;
  /**
   * @var string
   */
  protected $imageName;

  /**
   * @param string $query
   * @return ImageCategoryDB
   */
  public function setContentQuery($query)
  {
    $this->contentQuery = $query;

    return $this;
  }

  /**
   * @param string|array $availableList
   * @return ImageCategoryDB
   */
  public function setAvailableSize($availableList)
  {
    if (!is_array($availableList)) {
      $availableList = array($availableList);
    }
    $this->availableSize = $availableList;

    return $this;
  }

  /**
   * @param string $subPath
   * @return ImageCategoryDB
   */
  public function setSubPath($subPath)
  {
    $this->subPath = $subPath;

    return $this;
  }

  /**
   * @param string $imageName
   * @return ImageCategoryDB
   */
  public function setImageName($imageName)
  {
    $this->imageName = $imageName;

    return $this;
  }

  /**
   * @return string
   */
  public function getContentQuery()
  {
    return $this->contentQuery;
  }

  /**
   * @return array
   */
  public function getAvailableSize()
  {
    return $this->availableSize;
  }

  /**
   * @return string
   */
  public function getSubPath()
  {
    return $this->subPath;
  }

  /**
   * @param string $prop
   * @return string
   */
  public function getImageName($prop)
  {
    return strtr($this->imageName, ['?' => $prop]);
  }
}