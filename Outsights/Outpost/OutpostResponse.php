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
      $temp = $this;
      $temp->statusCode = $code;
      $temp->reasonPhrase = $reasonPhrase;
      return $temp;
    }

    public function getReasonPhrase()
    {
      return $this->reasonPhrase;
    }  
  }
  