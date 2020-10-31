<?php
	namespace Outsights\PageWeaver;

  use Outsights\Outstor\FileStorage;
  use Outsights\PageWeaver\AbstractPage;

  class Pagelet extends AbstractPage {

    private const PAGELETS_DIR = "outsights/resources/pageweaver/pagelets/";

    public function __construct(string $name) {
      if (preg_match(self::NAME_PATTERN, $name)) {
        $this->name = $name;
        $this->setPathByName($name);
      }
		}

    public function setPathByName($name)
    {
      $this->path = self::PAGELETS_DIR.$this->name.".pagelet";
    }
	}
