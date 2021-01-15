<?php
  namespace Outsights\Outpost;
  
  class OutpostResponse extends AbstractMessage
  {
    protected $statusCode;
    protected $reasonPhrase;

    # decided to add this as optionally
    protected $redirectCount = 0;
    protected $rawHeaders;

    /**
     * Class constructor.
     */
    public function __construct(int $statusCode, string $reasonPhrase = "")
    {
      $this->statusCode = $statusCode;
      $this->reasonPhrase = $reasonPhrase;
    }

    # response code
    
    public function getStatusCode() 
    {
      return $this->statusCode;
    }

    public function withStatus(int $code, string $reasonPhrase)
    {
      $this->statusCode = $code;
      $this->reasonPhrase = $reasonPhrase;
    }

    public function getReasonPhrase()
    {
      return $this->reasonPhrase;
    }

    # raw headers
    
    public function withRawHeaders(array $rawHeaders)
    {
      $this->rawHeaders = $rawHeaders;
    }

    public function withoutRawHeaders()
    {
      $this->rawHeaders = array();
    }

    public function getRawHeaders()
    {
      return $this->rawHeaders;
    }

    # redirect count

    public function getRedirectCount()
    {
      return $this->redirectCount;
    }

    public function withRedirectCount(int $count)
    {
      $this->redirectCount = $count;
    }

    public function withoutRedirectCount()
    {
      $this->redirectCount = 0;
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
    public function withCookie(string $name, OutpostCookie $cookie)
    {
      $this->cookies[$name] = $cookie;
    }
  }
  