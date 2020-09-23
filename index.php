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

    public function echoHTTPRequest()
    {
      if (!function_exists("getallheaders")) {
        $getAllHeaders = function () {
          $headers = array();
          foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == "HTTP_") {
              $headers[str_replace(' ', '_', ucwords(strtolower((str_replace('_', ' ', substr($name, 5))))))] = $value;
            }
          }
          return $headers;
        };
      } else {
        $getAllHeaders = getallheaders();
      }


      $httpRequest = $getAllHeaders();
      
      foreach ($httpRequest as $header => $value) {
        echo "<br>".$header." : ".$value;
      }
    }
  }
  
  $request = new Request();
  echo $request->echoHTTPRequest();