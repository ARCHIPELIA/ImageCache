<?php

namespace Atoll\ImageCache\Category;

interface ImageCategoryInterface
{
  /**
   * @param string $name
   * @return ImageCategoryInterface
   */
  public function setName($name);

  /**
   * @return string
   */
  public function getName();
}