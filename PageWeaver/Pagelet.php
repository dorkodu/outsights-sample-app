<?php
	namespace Outsights\PageWeaver;

  use Outsights\Outstor\FileStorage;
  use Outsights\PageWeaver\AbstractPage;

  class Pagelet extends AbstractPage {

    private const PAGELETS_DIR = "pagelets/";

    public function setPathByName($name)
    {
      $this->path = self::PAGELETS_DIR.$this->name.".pagelet";
    }
	}
