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
     * Returns an input value from the parsed body.
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
     * Returns the parsed body of this request
     **/
    public function getParsedBody()
    {
      return $this->parsedBody;
    }

    /**
     * Set this request with given body
     * 
     **/
    public function withBody(string $body)
    {
      $this->body = $body;
    }

    /**
     * Set this request with given body
     * 
     **/
    public function withParsedBody(array $parsedBody)
    {
      $this->parsedBody = $parsedBody;        
    }

    /**
     * Set this request without body
     *
     * @return void
     */
    public function withoutBody()
    {
      $this->body = "";
    }

    /**
     * Set this request without parsed body
     *
     * @return void
     */
    public function withoutParsedBody()
    {
      $this->parsedBody = array();
    }

    # FILES

    /**
     * Gets a file or a file array from the Request.
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
     * Gets the files array from the Request.
     *
     * @param string $name Key to look up in query
     * 
     **/
    public function getFiles()
    {
      return $this->files; 
    }

    /**
     * Checks if request has file.
     *
     * @param string $name OutpostFile's input name to look up in query
     * 
     * @return bool true if has, false otherwise  
     **/
    public function hasFile($name)
    {
      if (isset($this->files[$name])) {
        return true;
      } else return false;
    }

    /**
     * Set this request with the given file input
     *
     * @param string $inputName Input name to use as a key to your passed file input
     * @param mixed $file Can be a single OutpostFile instance, or an array of OutpostFile instances
     * @return void
     */
    public function withFile(string $inputName, $file)
    {
      $instanceFilter = function ($instanceCandidate) {
        return ($instanceCandidate instanceof OutpostFile);
      };

      if (is_array($file)) {
        # my first usage of map-filter-reduce in PHP :D
        $filteredResults = array_filter($file, $instanceFilter);
        $this->files[$inputName] = $filteredResults;
      }
      
      if ($file instanceof OutpostFile) {
        # if an OutpostFile instance, simply push to files array
        $this->files[$inputName] = $file;
      }
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
  