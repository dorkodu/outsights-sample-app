<?php
  namespace Outsights\Outpost;

  /**
   * The Outpost HTTP Library for Outsights Web Framework
   * @author Doruk Dorkodu <doruk@dorkodu.com>
   */
  class Outpost
  {
    # the cookie jar file to keep cookies from Outpost transactions
    protected const OUTPOST_COOKIEJAR = "resources/cookiejar";

    private static function httpRequestHeaders()
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
      return \trim(\str_replace(' ', '-', \ucwords(strtolower(str_replace('_', ' ', str_replace('-', '_', $name))))));
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
      return new OutpostRequest($method, $uri);
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
      return new OutpostResponse($code, $reasonPhrase);
    }

    /**
     * Parses the Set-Cookie header value and generates a OutpostCookie object
     * @param string $header
     *
     * @return false on failure
     * @return OutpostCookie on success
     **/
    public static function parseCookieFromHeader(string $header)
    {
      /**
       * I am sorry, this was the worst function that i have ever written.
       * this is my fault, i messed things up. you are free to complain to me via :
       *
       * i am : doruk dorkodu
       * email : doruk@dorkodu.com
       * wanderlyf : @doruk
       * twitter : @dorukdorkodu
       */

      if (!empty($header)) {

        # define a temp array to hold the unparsed pairs
        $temp = explode(';', $header);
        $cookieAssoc = array(); # array to keep cookie properties

        $nameValuePair = explode('=', $temp[0], 2);

        # assigning some variables
        $cookieAssoc['name'] = $nameValuePair[0];
        $cookieAssoc['value'] = $nameValuePair[1];

        array_shift($temp);

        # trim all the elements in the array
        foreach ($temp as $element) {
          $parsedElement = explode("=", $element, 2);

          $optionKey = $parsedElement[0];
          $optionValue = (count($parsedElement) < 2) ? $parsedElement[0] : $parsedElement[1];

          $cookieAssoc[trim($optionKey)] = trim($optionValue);
        }

        $cookieName = trim($cookieAssoc['name']);
        $cookieValue = trim($cookieAssoc['value']);
        $cookieExpireTime = (array_key_exists('expires', $cookieAssoc)) ? $cookieAssoc['expires'] : "0";
        $cookiePath = (array_key_exists('path', $cookieAssoc)) ? $cookieAssoc['path'] : "";
        $cookieDomain = (array_key_exists('domain', $cookieAssoc)) ? $cookieAssoc['domain'] : "";
        $cookieSecureOnly = (array_key_exists('secure', $cookieAssoc)) ? true : false;
        $cookieHttpOnly = (array_key_exists('HttpOnly', $cookieAssoc)) ? true : false;

        # string to timestamp (int) conversion
        $cookieTimestamp = strtotime($cookieExpireTime);

        return new OutpostCookie($cookieName, $cookieValue, $cookieTimestamp, $cookiePath, $cookieDomain, $cookieSecureOnly, $cookieHttpOnly);

      } else return false;
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
     * @param OutpostResponse $response
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
      # BİTMEDİ DAHA! return a body, content type etc.
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
     * @param array $options If you wish, you can pass CURLOPT_* options, which will be set.
     *
     * @return OutpostResponse
     */
    public static function sendRequest(OutpostRequest $request, array $options = [])
    {
      $curl = new CurlHTTP();

      # SET METHOD AND TARGET URL
      $curl->setURL($request->getRequestTarget());
      $curl->setMethod($request->getMethod());

      # SET DEFAULT OPTIONS
      $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
      $curl->setOpt(CURLOPT_MAXREDIRS, 10);
      $curl->setOpt(CURLOPT_COOKIEFILE, self::OUTPOST_COOKIEJAR);
      $curl->setOpt(CURLOPT_COOKIEJAR, self::OUTPOST_COOKIEJAR);

      # SET OPTIONS
      if (count($options) > 0) {
        foreach ($options as $name => $value) {
          $curl->setOpt($name, $value);
        }
      }

      # SET COOKIES
      if (count($request->getCookies()) > 0) {
        $curl->setCookies($request->getCookies());
      }

      # SET FILES (array of OutpostFile)
      
      # payload array that contains OutpostFile objects
      $filePayload = array();
      $requestFiles = $request->getFiles();

      foreach ($requestFiles as $key => $sentFile) {
        /**
         * PROBLEM : What if $sentFile is an array? GOD DAMN!
         * TODO: Find a way to handle an array of OutpostFile's
         */

        # convert each OutpostFile desired to be sent, to CURLFile
        # if array, iterate and convert, filter
        if (is_array($sentFile)) {
          $convertedSentFileArray = array();
          foreach ($sentFile as $sentFileItem) {
            if ($sentFileItem instanceof OutpostFile) {
              $convertedItem = self::convertToCURLFile($sentFileItem);
              # pushes only if item is converted correctly
              if ($convertedItem !== false && $convertedItem instanceof \CURLFile) {
                array_push($convertedSentFileArray, $convertedItem);
              }
            }
          }

          $filePayload[$key] = $convertedSentFileArray;
        } else {
          if ($sentFile instanceof OutpostFile) {
            $convertedSentFile = self::convertToCURLFile($sentFile);
            # adds only if converted correctly
            if ($convertedSentFile !== false && $convertedSentFile instanceof \CURLFile) {
              $filePayload[$key] = $convertedSentFile;
            }
          }
        }   

      }


      # SET BODY
      
      /**
       * When method is POST, attach a body from result of a decision.
       *
       * $curlPostBody : the wrapper for HTTP Body
       * 
       * If files exist merge like this --> [(parsed body) + files)]
       * If no files but parsed body, send it directly
       * If only text body, send it directly
       */
      if (strtoupper($request->getMethod()) == "POST") {
        if (!empty($filePayload)) {
          
          /**
           * It sends parsed body first, because there may be a CSRF token 
           * or any crucial POST fields, god knows...
           * 
           * NOTICE : array_merge() function WILL NOT overwrite existing fields. 
           *          the first parameter array's values will be preserved AS IS.
           */
          $curlPostBody = array_merge($request->getParsedBody(), $filePayload);

        } else if (!empty($request->getParsedBody())) {
          # send only parsed body
          $curlPostBody = $request->getParsedBody();
        } else {
          # send only text
          $curlPostBody = $request->getBody();
        }

        $curl->setBody($curlPostBody);
      }

      # SEND HEADERS
      $curl->setHeaders(self::serializeHeaders($request->getHeaders()));
      $curl->execute();
      
      # construct an OutpostResponse from CurlHTTP
      return self::constructResponseFromCURL($curl);
    }

    /**
     * Constructs an OutpostResponse from a finished CurlHTTP request
     *
     * @param CurlHTTP $curl Should be a finished request CurlHTTP object 
     * @return OutpostResponse response to a OutpostRequest sent
     **/
    private static function constructResponseFromCURL(CurlHTTP $curl)
    {
      $responseHeaders = $curl->getResponseHeaders();
      $responseBody = $curl->getResponseBody();

      # parse raw headers, split different landings
      # (on redirects, create a different array of headers for the new location)
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
        # default response status
        $protocolVersion = "1.1";
        $statusCode = "200";
        $reasonPhrase = "OK";
      }

      # CONSTRUCT a new OutpostResponse

      # have to add headers, body, cookies, files, and other information...
      $response = self::createResponse($statusCode, $reasonPhrase);
      $response->withProtocolVersion($protocolVersion);

      # optional info about HTTP lifecycle
      $response->withRedirectCount($redirectCount);
      $response->withRawHeaders($parsedHeaders);


      # generate cookies from all received Set-Cookie headers

      $cookieStrings = [];

      foreach ($landedPageHeaders as $header) {
        # check if a Set-Cookie header
        if (preg_match("/^Set-Cookie:(.*)$/", $header, $parsedResults)) {
          $cookieStrings[] = $parsedResults[1];
        }
      }

      if (!empty($cookieStrings)) {
        foreach ($cookieStrings as $cookieString) {
          $cookie = self::parseCookieFromHeader($cookieString);

          if ($cookie !== false) {
            $response->withCookie($cookie->getName(), $cookie);
          }
        }
      }

      $parsedHeaders = self::parseHTTPHeaders($landedPageHeaders);

      # set header
      $response->withHeaders($parsedHeaders);

      # set body
      $response->withBody($responseBody);

      # finally, oh my god its done! :D
      return $response;
    }

    /**
     * Parses the raw HTTP headers. Generates an array in a format like :
     * ['name' => 'value']
     *
     * @param array $rawHeaders Raw HTTP headers
     *
     * @return array A parsed headers array that contains headers in a relational way
     */
    private static function parseHTTPHeaders(array $rawHeaders)
    {
      /**
       * I DID HERE:
       * Added support for multiple Set-Cookie headers.
       */

      $parsedHeaders = array();

      foreach ($rawHeaders as $header) {

        $splittedHeader = explode(':', $header, 2);

        $headerName = trim($splittedHeader[0]);
        $headerValue = trim($splittedHeader[1]);

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
     * Creates a request object from the present HTTP request.
     *
     * @return OutpostRequest $request
     **/
    public static function currentRequest()
    {
      $method = (isset($_SERVER['REQUEST_METHOD']) && !empty($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : 'GET' ;
      $uri = (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '/' ;

      $request = new OutpostRequest($method, $uri);

      # cookies
      foreach ($_COOKIE as $name => $value) {
        $request->withCookie($name, $value);
      }

      # headers
      $requestHeaders = self::httpRequestHeaders();
      foreach ($requestHeaders as $name => $value) {
        $request->withHeader($name, $value);
      }

      # protocol version
      $protocolVersion = explode('/', $_SERVER['SERVER_PROTOCOL'])[1];
      $request->withProtocolVersion($protocolVersion);

      # target
      $request->withRequestTarget(self::getUrl());

      # post - parsed body
      $request->withParsedBody($_POST);

      # post - raw input string
      $rawPostInput = file_get_contents("php://input");
      $request->withBody($rawPostInput);

      # get (url query string)
      $request->withQuery($_GET);

       /* 
          SAMPLE POST FILE PSUEDO-ARRAY
          -----------------------------------------------------
          Array
          (
            [files] => Array
            (
              [name] => sample.zip
              [type] => application/zip
              [tmp_name] => /etc/lamp/php/tmp/phpLHc9Ym
              [error] => 0
              [size] => 17453481
            )
          )
       */

      # I DID HERE:
      # create OutpostFile objects from sent $_FILES global, and attach to current $request

      # parse and make $_FILES array "tidy", which can include distributed properties
      $parsedUploadsInfo = self::parseUploadsInfo($_FILES);

      if($parsedUploadsInfo !== false) {

        foreach ($parsedUploadsInfo as $uploadInput => $uploadInfo) {

          if (is_array($uploadInfo)) {
            # multiple files are sent under a single input
            # so convert them, add under an array            
            $multiFileArray = array();

            # convert upload info to OutpostFile
            foreach ($uploadInfo as $uploadItem) {
              array_push($multiFileArray, new OutpostFile($uploadItem['tmp_name'], $uploadInput, $uploadItem['name']));
            }

            # add as a file list
            $request->withFile($uploadInput, $multiFileArray);

          } else {
            $request->withFile($uploadInput, new OutpostFile($uploadInfo['tmp_name'], $uploadInput, $uploadInfo['name']));
          }
        }
      }

      return $request;
    }

    /**
     * Parses the uploaded files info from the given array. (e.g. $_FILES)
     * It beautifes the array, simply makes it "tidy"
     *
     * @param array $uploadedFiles
     *
     * @return array
     */
    private static function parseUploadsInfo(array $uploadedFiles)
    {
      $parsedInfo = array();
      
      foreach ($uploadedFiles as $key => $value) {
  
        if (is_array($value['name'])) {
          
          $parsedInfo[$key] = array();
          $fileCount = count($value['name']);
  
          for ($i = 0; $i < $fileCount; $i++) {
            $file = array();
  
            $file['name'] = $value['name'][$i];
            $file['type'] = $value['type'][$i];
            $file['tmp_name'] = $value['tmp_name'][$i];
            $file['error'] = $value['error'][$i];
            $file['size'] = $value['size'][$i];
  
            array_push($parsedInfo[$key], $file);
          }
  
        } else {
          $parsedInfo[$key] = $value; # it is beautiful anyways ;)
        }
      }
  
      return $parsedInfo;
    }

    /**
     * Converts an OutpostFile to CURLFile.
     * This CURLFile can be used to send files using cURL
     *
     * @param OutpostFile $outpostFile
     *
     * @return CURLFile on success
     * @return false on failure
     */
    private static function convertToCURLFile(OutpostFile $outpostFile)
    {
      if ($outpostFile->exists()) {
        $curlFile = curl_file_create($outpostFile->path(), $outpostFile->mimeType(), $outpostFile->name());
        return $curlFile;
      } else {
        return false; # file does not exist, so can't be sent
      }
    }

    /**
     * Get the URL for this request
     *
     * @return string $url
     */
    public static function getUrl()
    {
      $host = $_SERVER['HTTP_HOST'];
      $protocol = self::isSecureRequest() ? 'https' : 'http';
      $uri = $_SERVER['REQUEST_URI'];
      return $protocol . "://" . $host . $uri;
    }

    /**
     * Tells if the current request is secure (send over HTTPS)
     *
     * @return boolean
     */
    public static function isSecureRequest()
    {
      if (!empty($_SERVER['HTTPS']) && !is_null($_SERVER['HTTPS'])) {
        return true;
      } else return false;
    }
  }
