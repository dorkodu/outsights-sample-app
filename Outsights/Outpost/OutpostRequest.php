<?php
  namespace Outsights\Outpost;
  
  class OutpostRequest extends AbstractMessage
  {
    protected string $target;
    protected string $method;
    protected string $uri;

    protected $query = array();

    public function __construct(string $method, string $url)
    {
      $this->method = strtoupper($method);
      $this->target = $url;
      
      $uri = $this->parseTargetUrl($url)['uri'];
    }
    
    /**
     * Parses the given URL, returns an array from parts
     *
     * @param string $targetUrl
     * 
     * @return array An array consists of 'protocol', 'domain', 'port', 'path', 'query', 'uri' keys and their values.
     * @return false on failure
     */
    protected function parseTargetUrl(string $targetUrl)
    {
      $url = [];
      if (preg_match("~^(([a-zA-Z0-9-_]+):\/\/)?\/?([^\/\.]+\.)*?([^\/\.]+\.[^:\/\s\.]{2,3}(\.[^:\/\s\.]{2,3})?)(:\d+)?($|\/)([^#?\s]+)?(.*?)?(#[\w\-]+)?$~", $targetUrl, $parsedUrl)) {
        $url = [];
        $url['protocol'] = $parsedUrl[2] ?? "http";
        $url['domain'] = $parsedUrl[3].$parsedUrl[4];
        
        $urlPort = trim($parsedUrl[6], ":");
        $url['port'] = (!empty($urlPort)) ? $urlPort : (($url['protocol'] == "https") ? "443" : "80");
        
        $url['path'] = (!empty($parsedUrl[8])) ? $parsedUrl : "/";
        $url['query'] = trim($parsedUrl[9], "?");
        
        $url['uri'] = $url['path'].$url['query'];

        return $url;
      } else return false;
    }
    
    public function getRequestTarget() 
    {
      return $this->target;
    }

    public function withRequestTarget(string $target)
    {
      $this->target = $target;
    }
    
    public function withMethod(string $method)
    {
      $this->method = strtoupper($method);
    }
    
    public function getUri()
    {
      return $this->uri;
    }

    public function withUri($uri)
    {
      $this->uri = $uri;
    }

    public function withoutUri()
    {
      $this->uri = "";
    }

    public function getQueryString()
    {
      return http_build_query($this->query);
    }

    public function withQuery(array $query)
    {
      $this->query = $query;
    }

    public function withoutQuery()
    {
      $this->query = array();
    }

    public function getMethod()
    {
      return $this->method;
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

    /**
     * IMPORTANT
     * ------------------ 
     * The withCookie() methods vary between HTTP messages.
     *  
     * For requests, we only need a "name"-"value" pair.
     * But for responses, we need complete OutpostCookie objects.
     */

    /**
     * Set this request with the given cookie
     **/
    public function withCookie(string $name, string $cookie)
    {
      $this->cookies[$name] = $cookie;
    }
  }
  