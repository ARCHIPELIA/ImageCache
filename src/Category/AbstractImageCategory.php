<?php

namespace Atoll\ImageCache\Category;

class AbstractImageCategory
{
  /**
   * @var string
   */
  protected $name;

  /**
   * @param string $name
   */
  public function __construct($name)
  {
    $this->name = $name;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return $this->name;
  }
}