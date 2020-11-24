<?php
  namespace Outsights\Outpost;
  
  /**
   * Cookie value object class for Outpost HTTP Cookies.
   */
  class OutpostCookie
  {  
    private $name;
    private $value;
    private $expiresAt = 0;
    private $path = "/";
    private $isSecureOnly = false;
    private $domain;
    private $httpOnly = false;
  
    /**
     * Creates a new cookie value object.
     *
     * @param string $name Name of the cookie.
     * @param string $value Value that cookie will store.
     * @param string $expiresAt Time in seconds that the cookie will be expired at.
     * @param string $path The path of the URI
     * @param boolean $isSecureOnly Tells if this cookie will be available only under secure HTTP - SSL
     * @param string $domain The domain of the URI
     * @param boolean $httpOnly Tells if this cookie will be only server-readable/writable
     *
     **/
    public function __construct(string $name, string $value = "", int $expiresAt = 0, string $path = "", string $domain = "", bool $isSecureOnly = false, bool $httpOnly = false)
    {
      $this->name = $name;
      $this->value = $value;
      $this->expiresAt = $expiresAt;
      $this->path = $path;
      $this->isSecureOnly = $isSecureOnly;
      $this->domain = $domain;
      $this->httpOnly = $httpOnly;
    }

    public function getName()
    {
      return $this->name;
    }

    public function getValue()
    {
      return $this->value;
    }

    public function getExpireTime()
    {
      return $this->expiresAt;
    }

    public function getPath()
    {
      return $this->path;
    }

    public function getDomain()
    {
      return $this->domain;
    }

    public function isHTTPOnly()
    {
      return $this->httpOnly;
    }

    public function isSecureOnly()
    {
      return $this->secure;
    }
  }
  