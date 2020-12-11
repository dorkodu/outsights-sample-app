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
      if (is_array($value)) {
        $contents = implode(',', $value);
      } else {
        $contents = $value;
      }

      $this->setHeader($name, $contents);      
    }

    public function withHeaders(array $headers)
    {
      foreach ($headers as $name => $value) {
        $this->setHeader($name, $value);
      }
    }

    public function withoutHeader($name)
    {
      unset($this->headers[$name]);
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
      $this->protocolVersion = $version;
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
     * Set this request with given body
     * 
     **/
    public function withBody($body)
    {
      if (is_array($body)) {
        $this->parsedBody = $body;        
      } else if (is_string($body)) {
        $this->body = $body;
      }
    }

    /**
     * Set this request without body
     *
     * @return void
     */
    public function withoutBody()
    {
      $this->body = "";
      $this->parsedBody = array();
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
      $this->files[$file->inputName()] = $file;
    }

    /**
     * Set this request without the given file
     *
     * @param string $filename
     * @return void
     */
    public function withoutFile(string $filename)
    {
      unset($this->files[$filename]);
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
     * IMPORTANT
     * ------------------ 
     * The withCookie() methods vary between HTTP messages.
     *  
     * For requests, we only need a "name"-"value" pair.
     * But for responses, we need complete OutpostCookie objects.
     */

    /**
     * Set this request without the given cookie
     **/
    public function withoutCookie($name)
    {
      unset($this->cookies[$name]);
    }
}
  