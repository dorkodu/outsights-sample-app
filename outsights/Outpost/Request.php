<?php

  namespace Outsights\Outpost;
  
  /**
   * Request class for Outpost HTTP library, used in Outsights ecosystem
   */
  class Request
  {
    public function path()
    {
      $dirName = dirname($_SERVER['SCRIPT_NAME']);
      $baseName = basename($_SERVER['SCRIPT_NAME']);
      return str_replace([$dirName, $baseName], null, $_SERVER['REQUEST_URI']);
    }

    public function url()
    {
      $currentPath = $this->path();
      return $url = $_SERVER['SERVER_NAME'].$currentPath;
    }

    public function method()
    {
      return $_SERVER['REQUEST_METHOD'];
    }

  }
  