<?php 
  namespace Outsights\Outpost;
  
  class CurlHTTP
  {
    protected $curl;

    protected $responseBody;
    protected $responseHeaders;

    /**
     * Class constructor.
     */
    public function __construct()
    {
      $this->curl = curl_init();
      $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }

    public function close()
    {
      curl_close($this->curl);
    }

    public function execute()
    {
      
      $headers = array();
      
      # header handler function
      $headerHandler = function($curl, $header) use (&$headers) {
        
        $len = strlen($header);

        # if header is an empty string, dont push it to status header list
        if(empty(trim($header))) {
          return $len;
        }      

        $headers[] = trim($header);
        return $len;
      };
      
      $this->setOpt(CURLOPT_HEADERFUNCTION, $headerHandler);
      $this->responseBody = curl_exec($this->curl);

      $this->responseHeaders = $headers;
    }

    private function parseHeaderName(string $name)
    {
      return trim(str_replace(' ', '-', ucwords(mb_strtolower(str_replace('_', ' ', str_replace('-', '_', $name)), "UTF-8"))));
    }
    
    public function setURL(string $url)
    {
      curl_setopt($this->curl, CURLOPT_URL, $url);
    }

    public function setOpt($var, $value)
    {
      curl_setopt($this->curl, $var, $value);
    }

    public function setMethod(string $method)
    {
      switch ($method) {
        case "GET":
          $this->setOpt(CURLOPT_HTTPGET, true);
          break;
        case "POST":
          $this->setOpt(CURLOPT_POST, true);
          break;
        default:
          $this->setOpt(CURLOPT_CUSTOMREQUEST, $method);
          break;
      }
    }    

    /**
     * Sets a string value of the Set-Cookie header 
     *
     * @param string $cookies
     * @return void
     */
    public function setCookies(array $cookies)
    {
      $cookiesArray = array();

      foreach ($cookies as $name => $value) {
        $line = $name."=".$value;
        array_push($cookiesArray, $line);
      }

      $cookiesString = implode("; ", $cookiesArray);

      $this->setOpt(CURLOPT_COOKIE, $cookiesString);
    }

    /**
     * Sets the body for a HTTP POST request
     * 
     * @param  $body
     * @return void
     */
    public function setBody($body)
    {
      $this->setOpt(CURLOPT_POSTFIELDS, $body);
    }

    /**
     * Sets the headers for the current request
     *
     * @param array $headers
     * @return void
     */
    public function setHeaders(array $headers)
    {
      $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    public function getErrors()
    {
      if (curl_errno($this->curl)) {
        return curl_error($this->curl);
      } else return null;
    }

    public function getResponseBody()
    {
      return $this->responseBody;
    }

    public function getResponseHeaders()
    {
      return $this->responseHeaders;
    }
  }
  