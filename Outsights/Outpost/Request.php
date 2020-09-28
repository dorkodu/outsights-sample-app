<?php

  namespace Outsights\Outpost;
  
  /**
   * Request class for Outpost HTTP library, used in Outsights ecosystem
   * 
   * @author Doruk Dorkodu <doruk@dorkodu.com>
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

    /**
     * Returns all input data for the given Request.
     *
     * @return array
     **/
    public function dataPayload()
    {
      
    }
    
    /**
     * Gets the value for a given key, from the url query.
     *
     * @param string $name Key to look up in query
     * @return mixed value for given key
     **/
    public function getFromQuery($name)
    {
      if (isset($_GET)) {
        if (isset($_GET[$name])) {
          return $_GET[$name];
        } else return null;  
      } else return null;
    }

    /**
     * Gets the value for a given cookie, from the Request.
     *
     * @param string $name Key to look up in query
     * 
     * @return mixed value for given key
     **/
    public function getCookie($name)
    {
      
    }

    /**
     * Gets the value for a given cookie, from the Request.
     *
     * @param string $name Key to look up in query
     * 
     * @return File file of a given input  
     **/
    public function getFile($name)
    {
      
    }
  }