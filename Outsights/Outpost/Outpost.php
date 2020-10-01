<?php
  namespace Outsights\Outpost;
  
  /**
   * The HTTP Library for Outsights
   * @author Doruk Dorkodu <doruk@dorkodu.com>
   */
  class Outpost
  {
    public static function httpRequestHeaders()
    {
      $headers = array();
      foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == "HTTP_") {
          $headers[str_replace(' ', '-', ucwords(strtolower((str_replace('_', ' ', substr($name, 5))))))] = $value;
        }
      }
      return $headers;
    }

    /**
     * Returns the user IP from request
     *
     * @return string the client IP
     */
    public function IP() {
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
  }
  