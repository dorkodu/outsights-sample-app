<?php

  namespace Outsights\Outpost;
  
  /**
   * Response class for Outpost HTTP library, used in Outsights ecosystem
   * 
   * @author Doruk Dorkodu <doruk@dorkodu.com>
   */
  class Response
  {
    /**
     * Adds a header to the Request
     *
     * @param string $name adds a header to the Response.
     * @param string $value value for given name.
     *
     * @return type
     **/
    public function header($name, $value)
    {

    }

    /**
     * Adds the headers to the Request
     *
     * @param array $headers Headers array to return with Response.
     *
     **/
    public function withHeaders($headers)
    {

    }

    /**
     * Returns a page with given options, as a Response
     *
     * @param string $pageName Name of the page to return.
     * @param array $data Data to give to the page.
     * @param string $responseCode HTTP Response code to return.
     *
     **/
    public function page($pageName, $data, $responseCode)
    {

    }

    /**
     * When returns a response, calls this callback
     *
     * @param function $callback a callable to execute
     *
     **/
    public function withCallback($callback)
    {

    }

    /**
     * Downloads a file
     *
     * @param string $path path of the file to download
     * @param string $name name of the downloaded file
     * @param boolean $deleteAfterSend if true, deletes the file after send.
     *
     **/
    public function download($path, $name, $deleteAfterSend)
    {

    }

    /**
     * Attaches a file to the Response
     *
     * @param string $path path of the file to download
     *
     **/
    public function attachFile($path)
    {

    }

    # redirect methods

    /**
     * 
     *
     * @param  $ description.
     *
     * @return type
     **/
    public function redirectAction()
    {
      # code...
    }


    /**
     * Redirects to a URL
     *
     * @param string $url URL to redirect to.
     *
     **/
    public function redirectToURL($url)
    {
      # code...
    }

    /**
     * Redirects to a page in application
     *
     * @param $pageName Page name that will be redirected to.
     * @param $statusCode optional HTTP status code.
     *
     **/
    public function redirectToPage($pageName, $statusCode = 200)
    {
      
    }
  }