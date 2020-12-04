<?php
  namespace Outsights\Outpost;
  
  abstract class AbstractMessage
  {
    protected $protocolVersion;

    protected $parsedBody = array();
    protected $body = "";

    protected $headers = array();
    protected $files = array();
    protected $cookies = array();

    # HEADERS

    public function getHeader(string $name)
    {
      $result = $this->headers[$this->parseHeaderName($name)];
      return explode(',', $result);
    }

    public function getHeaders()
    {
      return $this->headers;
    }

    protected function setHeader(string $name, $value)
    {
      $this->headers[$this->parseHeaderName($name)] = $value;
    }

    public function withHeader(string $name, $value)
    {
      $temp = $this;
      
      $contents = "";
      
      if (is_array($value)) {
        $contents = implode(',', $value);
      } else {
        $contents = $value;
      }
      
      $this->setHeader($name, $contents);      
      return $temp;
    }

    public function withHeaders(array $headers)
    {
      $temp = $this;
      foreach ($headers as $name => $value) {
        $temp->setHeader($name, $value);
      }

      return $temp;
    }

    public function withoutHeader($name)
    {
      $temp = $this;
      unset($temp->headers[$name]);
      return $temp;
    }
    
    protected function parseHeaderName(string $name)
    {
      return str_replace(' ', '-', ucwords(mb_strtolower(str_replace('_', ' ', str_replace('-', '_', $name)), "UTF-8")));
    }
    
    public function hasHeader(string $name)
    {
      if (array_key_exists($this->parseHeaderName($name), $this->headers)) {
        return true;
      } else return false;
    }

    public function getHeaderLine(string $name)
    {
      if (array_key_exists($this->parseHeaderName($name), $this->headers)) {
        return $this->headers[$name];
      } else {
        return null;
      }
    }

    # MISC...

    public function getProtocolVersion()
    {
      return $this->protocolVersion;
    }

    /**
     * Returns this request with given protocol version
     **/
    public function withProtocolVersion(string $version)
    {
      $temporary = $this;
      $temporary->protocolVersion = $version;
      return $temporary;
    }

    # BODY

    /**
     * Returns an input datum from the parsed body.
     **/
    public function getFromParsedBody($name)
    {
      if (is_array($this->parsedBody) && isset($this->parsedBody[$name])) {
        return $this->parsedBody[$name];
      } else {
        return null;
      }
    }

    /**
     * Returns the body of this request
     **/
    public function getBody()
    {
      return $this->body;
    }

    /**
     * Returns a copy of this request with given body
     * 
     **/
    public function withBody($body)
    {
      $temp = $this;
      
      if (is_array($body)) {
        $temp->parsedBody = $body;        
      } else if (is_string($body)) {
        $temp->body = $body;
      } else {}

      return $temp;
    }

    public function withoutBody()
    {
      $temp = $this;
      
      $temp->body = "";
      $temp->parsedBody = array();

      return $temp;
    }

    # FILES

    /**
     * Gets the value for a given cookie, from the Request.
     *
     * @param string $name Key to look up in query
     * 
     **/
    public function getFile($name)
    {
      if (isset($this->files[$name])) {
        return $this->files[$name]; 
      } else {
        return null;
      }
    }

    /**
     * Checks if request has file.
     *
     * @param string $name Key to look up in query
     * 
     * @return bool true if has, false else  
     **/
    public function hasFile($name)
    {
      if (isset($this->files[$name])) {
        return true;
      } else return false;
    }

    public function withFile(OutpostFile $file)
    {
      $temp = $this;
      $temp->files[$file->inputName()] = $file;
      return $temp;
    }

    public function withoutFile($filename)
    {
      $temp = $this;
      unset($temp->files[$filename]);
      return $temp;
    }

    # COOKIES

    /**
     * Returns the value of a given name in this request cookies
     *
     * @param string $name
     * @return mixed
     */
    public function getCookie(string $name)
    {
      return $this->cookies[$name];
    }

    /**
     * Returns the all request cookies
     *
     * @param string $name
     * @return array
     */
    public function getCookies()
    {
      return $this->cookies;
    }
    
    /**
     * Returns a copy of this request with given cookie
     **/
    public function withCookie($name, $value)
    {
      $temp = $this;
      $temp->cookies[$name] = $value;
      return $temp;
    }

    /**
     * Returns a copy of this request without given cookie
     **/
    public function withoutCookie($name)
    {
      $temp = $this;
      unset($temp->cookies[$name]);
      return $temp;
    }
  }
  