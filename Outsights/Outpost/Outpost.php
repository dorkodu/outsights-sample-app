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
      return trim(str_replace(' ', '-', ucwords(mb_strtolower(str_replace('_', ' ', str_replace('-', '_', $name)), "UTF-8"))));
    }

    /**
     * Creates an OutpostRequest object
     *
     * @param string $method
     * @param string $uri
     * @return OutpostRequest
     */
    public static function createRequest(string $method, string $uri): OutpostRequest
    {
      $request = new OutpostRequest($method, $uri);
      return $request;
    }

    /**
     * Creates an OutpostResponse object
     *
     * @param integer $code
     * @param string $reasonPhrase
     * @return OutpostResponse
     */
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

      $responseCookies = $response->getCookies();
      $responseCookieString = implode("; ", $responseCookies);
      header("Cookie: ".$responseCookieString);

      echo $response->body;
      # return a body, content type etc.
    }

    /**
     * Tells if the given header is a HTTP response status header
     *
     * @param string $header
     * @return boolean
     */
    private static function isHTTPStatusHeader(string $header)
    {
      # HTTP/1.1 302 Found
      if (preg_match("/([a-zA-Z]*)\/([0-9]*\.[0-9]*) (\d{3}) ([a-zA-Z0-9 ]+)/", $header)) {
        return true;
      } else {
        return false;
      }
    }

    /**
     * Parses the HTTP response status header.
     * The structure should be like :
     * "HTTP/1.1 302 Found"
     *
     * @param string $header
     * @return array if the given header is an HTTP response status header
     * @return false header is not an HTTP response status
     */
    private static function parseStatusHeader(string $header)
    {
      # HTTP/1.1 302 Found
      if (preg_match("/([a-zA-Z]*)\/([0-9]*\.[0-9]*) (\d{3}) ([a-zA-Z0-9 ]+)/", $header, $catch)) {
        $parsedStatus = array();

        $parsedStatus['status-header'] = $catch[0];
        $parsedStatus['protocol'] = $catch[1];
        $parsedStatus['protocol-version'] = $catch[2];
        $parsedStatus['status-code'] = $catch[3];
        $parsedStatus['reason-phrase'] = $catch[4];

        return $parsedStatus;
      } else {
        return false;
      }
    }

    /**
     * Parses the returned response headers.
     * For redirects, each page state is in a different array.
     * Returns an array that contains an array of headers for each response state
     *
     * @param array $headers Raw HTTP response headers
     * @return array Parsed headers
     */
    private static function parseRawResponseHeaders(array $headers)
    {
      $currentResponse = -1;
      $parsedHeaders = array();

      foreach ($headers as $header) {
        if (self::isHTTPStatusHeader($header)) {
          ++$currentResponse;
          $parsedHeaders[$currentResponse][] = $header;
        } else {
          if ($currentResponse == -1) {
            ++$currentResponse;
            $parsedHeaders[0][] = $header;
          } else {
            $parsedHeaders[$currentResponse][] = $header;
          }
        }
      }

      return $parsedHeaders;
    }

    /**
     * Makes a HTTP request with given HTTP Request Message
     *
     * @param OutpostRequest $request
     *
     * @return OutpostResponse $response
     */
    public static function sendRequest(OutpostRequest $request, array $options = [])
    {
      $curl = new CurlHTTP();

      # send with method to url
      $curl->setURL($request->getRequestTarget());
      $curl->setMethod($request->getMethod());

      # set default options
      $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
      $curl->setOpt(CURLOPT_MAXREDIRS, 20);

      # set options
      if (count($options) > 0) {
        foreach ($options as $name => $value) {
          $curl->setOpt($name, $value);
        }
      }

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

      $parsedHeaders = self::parseRawResponseHeaders($responseHeaders);

      # redirect count
      $redirectCount = count($parsedHeaders) - 1;

      $landedPageHeaders = $parsedHeaders[count($parsedHeaders) - 1];
      $statusHeader = $landedPageHeaders[0];

      # delete first line, which is HTTP status header
      array_shift($landedPageHeaders);

      # ···
      if (self::isHTTPStatusHeader($statusHeader)) {

        $parsedStatusHeader = self::parseStatusHeader($statusHeader);

        # response status
        $protocolVersion = $parsedStatusHeader['protocol-version'];
        $statusCode = $parsedStatusHeader['status-code'];
        $reasonPhrase = $parsedStatusHeader['reason-phrase'];
      } else {
        # defaults
        $protocolVersion = "1.1";
        $statusCode = "200";
        $reasonPhrase = "OK";
      }


      # generating an OutpostResponse
      # have to add headers, body, cookies, files, and other knowledge...
      $response = self::createResponse($statusCode, $reasonPhrase);
      $response = $response->withProtocolVersion($protocolVersion);

      # set header
      $parsedLandedPageHeaders = self::parseHTTPHeaders($landedPageHeaders);
      $response = $response->withHeaders($parsedLandedPageHeaders);

      # set body
      $response = $response->withBody($responseBody);
    
      # finally, oh my god its done
      return $response;
    }

    /**
     * Parses the raw HTTP headers. Generates an array in a format like :
     * ['name' => 'value'] or ['name' => ['1','2'...]]
     *
     * @param array $rawHeaders Raw HTTP headers
     * @return array A parsed headers array that contains headers in a relational way
     */
    public static function parseHTTPHeaders(array $rawHeaders)
    {
      $parsedHeaders = array();

      foreach ($rawHeaders as $header) {
        
        $splittedHeader = explode(':', $header, 2);

        $headerName = trim($splittedHeader[0]);
        $headerValueString = trim($splittedHeader[1]);

        # parse the header line
        $headerValue = ""; # just definition, in default, string type :D
        if (strstr($headerValueString, ",") !== false) {
          $headerValue = explode(',', $headerValueString);
        } else {
          $headerValue = trim($headerValueString);
        }

        # pushing the parsed header line to $parsedHeaders
        $parsedHeaders[self::parseHeaderName($headerName)] = $headerValue;
      }

      return $parsedHeaders;
    }

    /**
     * Returns the client IP address from current request
     *
     * @return string the client IP
     */
    public static function getIP()
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
