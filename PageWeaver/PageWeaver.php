<?php
  namespace Outsights\PageWeaver;

  use Outsights\Outstor\FileStorage;
  use Outsights\PageWeaver\Page;

  /**
   * A minimalist Template Engine for Outsights ecosystem
   */
  class PageWeaver
  {
    protected const STATIC_PAGE_DIR = "static-pages/";

    /**
     * Checks whether a page exists.
     *
     * @param string $pageName.
     *
     * @return boolean true if exists, false otherwise
     **/
    public static function pageExists(string $pageName)
    {
      $tempPage = new Page($pageName);
      return $tempPage->isUseful();
    }

    /**
     * Renders a particular page.
     *
     * @param string $pageName
     * @param array $data the data to fill into the page
     *
     * @return boolean true on success, false on failure
     **/
    public static function render(string $pageContents)
    {
      echo $pageContents;
    }

    /**
     * Composes a particular page.
     *
     * @param string $pageName
     * @param array $data
     *
     * @return string Composed page contents on, success
     * @return false on failure
     **/
    public static function composePage(string $pageName, array $data)
    {
      $page = new Page($pageName);
      if($page->retrieve() !== false) {
        $page->seedPagelets();
        $page->seedData($data);
        return $page->getContents();
      } else return false;
    }

    /**
     * Composes a static page content.
     *
     * @param string $pageName
     *
     * @return string Composed page contents on, success
     * @return false on failure
     **/
    public static function composeStaticPage(string $pageName)
    {
      $staticPagePath = self::STATIC_PAGE_DIR.$pageName.".html";
      if (self::staticPageExists($pageName)) {
        $contents = FileStorage::getFileContents($staticPagePath);
        if ($contents !== false) {
          return $contents;
        } else return false; # cannot read the file
      } else return false; # static file is not useful
    }

    /**
     * Checks if a particular static page exists.
     *
     * @param string $pageName
     *
     * @return boolean true on success, false on failure
     **/
    public static function staticPageExists($pageName)
    {
      if (preg_match("/([a-zA-Z0-9-_]+)/", $pageName)) {
        if (FileStorage::isUsefulDirectory(self::STATIC_PAGE_DIR)) {
          $staticPagePath = self::STATIC_PAGE_DIR.$pageName.".html";
          if (FileStorage::isUsefulFile($staticPagePath)) {
            return true;
          } else return false; # static file is not useful
        } else return false; # static file directory is not useful
      } else return false; # page name breaches the rule
    }
  }