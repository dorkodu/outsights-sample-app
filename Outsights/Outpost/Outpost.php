<?php
  namespace Outsights\Outpost;

  /**
   * The Outpost HTTP Library for Outsights Web Framework
   * @author Doruk Dorkodu <doruk@dorkodu.com>
   */
  class Outpost
  {
    public static function httpRequestHeaders()
    {
      $headers = array();
      foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == "HTTP_") {
          $headers[self::parseHeaderName(substr($name, 5))] = $value;
        }
      }
      return $headers;
    }

    protected static function parseHeaderName(string $name)
    {
      return str_replace(' ', '-', ucwords(mb_strtolower(str_replace('_', ' ', str_replace('-', '_', $name)), "UTF-8")));
    }

    public static function createRequest(string $method, string $uri): OutpostRequest
    {
      $request = new OutpostRequest($method, $uri);
      return $request;
    }

    public static function createResponse(int $code = 200, string $reasonPhrase = 'OK'): OutpostResponse
    {
      $response = new OutpostResponse($code, $reasonPhrase);
      return $response;
    }

    /**
     * Converts the parsed HTTP headers array into an array of header lines
     *
     * @param array $headers
     * @return array
     */
    private static function serializeHeaders(array $headers)
    {
      $serializedHeaders = array();
      
      foreach($headers as $name => $value) {
        if (is_array($value)) {
          $headerLine = $name.": ".implode(',', $value);
        } else {
          $headerLine = $name.": ".$value;
        }

        array_push($serializedHeaders, $headerLine);
      }

      return $serializedHeaders;
    }

    /**
     * Responses (literrally) to current request with a given HTTP Response Message
     *
     * @param ResponseInterface $response
     */
    public static function returnResponse(OutpostResponse $response)
    {
      http_response_code($response->getStatusCode());
      
      $responseHeaders = self::serializeHeaders($response->getHeaders());

      foreach ($responseHeaders as $line) {
        header($line);
      }
      # return a body, content type etc.
    }

    /**
     * Makes a HTTP request with given HTTP Request Message
     *
     * @param RequestInterface $request
     * 
     * @return ResponseInterface $response
     */
    public static function sendRequest(OutpostRequest $request)
    {
      $curl = new CurlHTTP();

      # send with method to url
      $curl->setURL($request->getRequestTarget());
      $curl->setMethod($request->getMethod());

      # send cookies
      if (count($request->getCookies()) > 0) {
        $curl->setCookies($request->getCookies());
      }

      # send headers
      $curl->setHeaders(self::serializeHeaders($request->getHeaders()));

      $curl->execute();

      # generating the outpost http response
      $responseHeaders = $curl->getResponseHeaders();
      $responseBody = $curl->getResponseBody();
            
      $parsedHeaders = array();
      
      $response = self::createResponse();

 
    }    

    /**
     * Returns the client IP address from request
     *
     * @return string the client IP
     */
    public static function IP()
    {
			if(getenv('HTTP_CLIENT_IP')) {
				$ip = getenv('HTTP_CLIENT_IP');
			} elseif(getenv('HTTP_X_FORWARDED_FOR')) {
				$ip = getenv('HTTP_X_FORWARDED_FOR');
				if(strstr($ip, ',')) {
					$temp = explode(',',$ip);
					$ip = trim($temp[0]);
				}
			} else {
				$ip = getenv('REMOTE_ADDR');
			}
			return $ip;
    }

    /**
     * Creates a request object from the present HTTP $_SERVER request.
     *
     * @return OutpostRequest $request
     **/
    public function currentRequest()
    {
      $method = (isset($_SERVER['REQUEST_METHOD']) && !empty($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : 'GET' ;
      $uri = (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '/' ;

      $request = new OutpostRequest($method, $uri);

      foreach ($_COOKIE as $name => $value) {
        $request = $request->withCookie($name, $value);        
      }


      $request = $request->withHeaders($this->httpRequestHeaders());

      $protocolVersion = explode('/', $_SERVER['SERVER_PROTOCOL'])[1];
      $request = $request->withProtocolVersion($protocolVersion);

      $request = $request->withRequestTarget(self::getUrl());

      $request = $request->withBody($_POST);

      $request = $request->withQuery($_GET);

      return $request;

    }

    /**
     * Get the URL for this request
     *
     * @return string $url
     */
    public static function getUrl()
    {
      $host = $_SERVER['HTTP_HOST'] . $_SERVER['SERVER_PORT'];
      $protocol = self::isSecureConnection() ? 'https' : 'http';
      $uri = $_SERVER['REQUEST_URI'];
      return $protocol . "://" . $host . $uri;
    }

    /**
     * Tells if the current request is secure (send over HTTPS)
     *
     * @return boolean
     */
    public static function isSecureConnection()
    {
      if (!empty($_SERVER['HTTPS']) && !is_null($_SERVER['HTTPS'])) {
        return true;
      } else return false;
    }
  }
  