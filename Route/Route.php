<?php
  namespace Outsights\Route;

  use Outsights\OdEngine\Od;

  class Route
  {
    /**
     * Parses and returns the Request URI
     *
     * @return string URI for the current Request
     */
    private static function parseURI()
    {
      $dirName = dirname($_SERVER['SCRIPT_NAME']);
      $baseName = basename($_SERVER['SCRIPT_NAME']);
      return str_replace([$dirName, $baseName], null, $_SERVER['REQUEST_URI']);
    }

    /**
     * Runs the Outsights Router
     *
     * @param string $routeURL
     * @param mixed $routeAction
     * @param string $acceptedHTTPVerbs Pattern : 'GET|POST|PATCH...' or 'GET' for single option
     * 
     * @return void
     */
    public static function run(string $routeURL, $routeAction, string $acceptedHTTPVerbs = "GET")
    {
      $requestURI = self::parseURI();
      if (preg_match('@^'.$requestURI.'$@', $routeURL, $parsedDataFromURI)) {
        if (is_callable($routeAction))
          call_user_func_array($routeAction, $parsedDataFromURI);
        else {
          if (is_string($routeAction)) {
            list($routeMethod, $routeController) = explode('@', $routeAction);
            call_user_func_array([new $routeController, $routeMethod], $parsedDataFromURI);
          } # else invalid route action.            
        }
      }
    }

    /**
     * Serves a static page for the given URL
     * 
     * @param string $routeURL
     * @param string $staticPageName
     * 
     * @return void
     */ 
    public static function staticPage(string $routeURL, string $staticPageName)
    {
      
    }

    /**
     * Redirects the current route
     *
     * @param string $from
     * @param string $to
     * @return void
     */
    public static function redirect(string $from, string $to)
    {
      $requestURI = self::parseURI();
      if (preg_match('@^'.$requestURI.'$@', $from, $parsedDataFromUri)) {
        Route::run($from);
      }
    }

    /**
     * 
     *
     * @return void
     */
    public static function current()
    {

    }
      
    # routes to the previous route, which is saved.
    public static function back()
    {
      
    }

  } 