<?php

  namespace Outsights\Outpost\Cookie;
  
  /**
   * Cookie representer class for Outsights ecosystem.
   */
  class Cookie
  {
    /**
     * Gets the cookie.
     *
     * @param string $name
     *
     * @return string value of the cookie
     **/
    public static function get(string $name)
    {
      if (isset($_COOKIE[$name])) {
        
      } else {
        
      }
    }

    /**
     * Undocumented function.
     *
     * @param string $name description.
     * @param string $value description.
     * @param string $expiresAt description.
     * @param string $path description.
     * @param string $domain description.
     * @param boolean $httpOnly description.
     *
     * @return type
     **/
    public static function bake($name, $value, $expiresAt, $path, $domain, $secure, $httpOnly = false)
    {
      
    }
  }
  