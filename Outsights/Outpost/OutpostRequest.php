<?php
  namespace Outsights\Outpost;
  
  class OutpostRequest extends AbstractMessage
  {
    protected $target;
    protected $method;
    protected $uri;

    protected $query = array();

    public function __construct($method, $url)
    {
      $this->method = strtoupper($method);
      $this->target = $url;
    }

    public function getRequestTarget() 
    {
      return $this->target;
    }

    public function withRequestTarget($target)
    {
      $temp = $this;
      $temp->target = $target;
      return $temp;
    }
    
    public function withMethod($method)
    {
      $temp = $this;
      $temp->method = strtoupper($method);
      return $temp;
    }
    
    public function getUri()
    {
      return $this->uri;
    }

    public function withUri($uri)
    {
      $temp = $this;
      $temp->uri = $uri;
      return $temp;
    }   

    public function getQueryString()
    {
      return http_build_query($this->query);
    }

    public function withQuery(array $query)
    {
      $temp = $this;
      $temp->query = $query;
      return $temp;
    }

    public function getMethod()
    {
      return $this->method;
    }

    public function isHttps()
    {
      if ($this->protocol == "HTTPS") {
        return true;
      } else return false;
    }
    
    /**
     * Gets the value for a given key, from the url query.
     *
     * @param string $name Key to look up in query
     * 
     * @return mixed value for given key
     **/
    public function getFromQuery($name)
    {
      if (isset($this->query[$name])) {
        return $this->query[$name];
      } else return null;  
    }
  }
  