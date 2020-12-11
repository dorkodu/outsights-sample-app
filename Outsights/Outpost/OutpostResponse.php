<?php
  namespace Outsights\Outpost;
  
  class OutpostResponse extends AbstractMessage
  {
    protected $statusCode;
    protected $reasonPhrase;

    /**
     * Class constructor.
     */
    public function __construct(int $statusCode, string $reasonPhrase = "")
    {
      $this->statusCode = $statusCode;
      $this->reasonPhrase = $reasonPhrase;
    }
    
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
  