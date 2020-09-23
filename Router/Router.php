<?php
  namespace Outsights\Router;

  class Router
  {
    public static function parseUrl()
    {
      $dirName = dirname($_SERVER['SCRIPT_NAME']);
      $baseName = basename($_SERVER['SCRIPT_NAME']);
      return str_replace([$dirName, $baseName], null, $_SERVER['REQUEST_URI']);
    }

    # routes to the given URL
    public static function run(string $routeUrl, $routeAction, string $acceptedHTTPVerbs = "GET")
    {
      $requestUri = self::parseUrl();
      if (preg_match('@^'.$requestUri.'$@', $routeUrl, $parsedDataFromUri)) {
        if (is_callable($routeAction))
          call_user_func_array($routeAction, $parsedDataFromUri);
        else {
          if (is_string($routeAction)) {
            list($routeMethod, $routeController) = explode('@', $routeAction);
            call_user_func_array([new $routeController, $routeMethod], $parsedDataFromUri);
          } # else invalid route action.            
        }
      }
    }

    # serves a static page for the given URL
    public static function staticPage(string $routeUrl, string $staticPageName)
    {
      
    }

    # serves a static page for the given URL
    public static function redirect(string $routeUrl, string $staticPageName)
    {
      
    }

    # returns the current Route object
    public static function current(string $routeUrl, string $staticPageName)
    {

    }
      
    # routes to the previous route, which is saved.
    public static function back(string $routeUrl, string $staticPageName)
    {
      
    }

  } 