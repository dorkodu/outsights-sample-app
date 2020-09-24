<?php
  namespace Outsights\PageWeaver;
  
  /**
   * A minimalist Template Engine for Outsights ecosystem
   */
  class PageWeaver
  {
    /**
     * Checks for if a page exists.
     *
     * @param string $pageName.
     *
     * @return boolean true if exists, false otherwise
     **/
    public static function pageExists(string $pageName)
    {
      
    }

    /**
     * Renders a particular page.
     *
     * @param string $pageName
     * @param array $data the data to fill into the page
     *
     * @return boolean true on success, false on failure
     **/
    public function render(string $pageContents, array $data)
    {
      # code...
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
    public function composePage(string $pageName, array $data)
    {
      # code...
    }

    /**
     * Composes a static page content.
     *
     * @param string $pageName
     *
     * @return string Composed page contents on, success
     * @return false on failure
     **/
    public function composeStaticPage(string $pageName)
    {
      
    }
  }
  