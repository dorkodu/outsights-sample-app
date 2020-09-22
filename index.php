<?php
  require_once 'loot/loom-weaver.php';
  /*
  require_once 'outsights/bootstrap.php';*/

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
      /*
        $currentPath = $this->path();
        return $url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
      */
      $host = $_SERVER['HTTP_HOST'];
      $protocol = $this->isHttps() ? 'https' : 'http';
      return $protocol."://".$host.$this->path();
    }

    public function method()
    {
      return $_SERVER['REQUEST_METHOD'];
    }

    public function isHttps()
    {
      if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) {
        return true;
      } else return false;
    }

    public function webServerRoot()
    {
      return str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']);
    }
  }
  
  $request = new Request();
  echo $request->url();