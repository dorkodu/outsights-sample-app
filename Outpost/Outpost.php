<?php
  namespace Outsights\Outpost;
  
  /**
   * The HTTP Library for Outsights
   * @author Doruk Dorkodu <doruk@dorkodu.com>
   */
  class Outpost
  {
    public static function httpRequestHeaders()
    {
      $headers = array();
      foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == "HTTP_") {
          $headers[str_replace(' ', '-', ucwords(strtolower((str_replace('_', ' ', substr($name, 5))))))] = $value;
        }
      }
      return $headers;
    }
  }
  