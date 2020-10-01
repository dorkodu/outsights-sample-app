<?php

  namespace Outsights\Shepherd;

  /**
   * Shepherd logging library for Outsights ecosystem.
   */
  class Shepherd
  {
    public const EMERGENCY = 1;
    public const ALERT = 2;
    public const CRITICAL = 3;
    public const ERROR = 4;
    public const WARNING = 5;
    public const NOTICE = 6;
    public const INFO = 7;
    public const DEBUG = 8;

    private const LOG_DIR = "outsights/resources/logs";

    /**
     * Logs the content to the given file.
     *
     * @param int $type Importance level of the log. Enter a const, Shepherd::ERROR, Shepherd::NOTICE etc.
     * @param string $filePath File to log.
     * @param string $content Content of the log entry.
     *
     * @return boolean true on success, false on failure.
     **/
    public static function log(int $type, string $filePath, string $content)
    {
      switch ($type) {
        case 1:
          $importanceTitle = "EMERGENCY";
          break;
        case 2:
          $importanceTitle = "ALERT";
          break;
        case 3:
          $importanceTitle = "CRITICAL";
          break;
        case 4:
          $importanceTitle = "ERROR";
          break;   
        case 5:
          $importanceTitle = "WARNING";
          break;
        case 6:
          $importanceTitle = "NOTICE";
          break;
        case 7:
          $importanceTitle = "INFO";
          break;
        case 8:
          $importanceTitle = "DEBUG";
          break;
        default:
          $importanceTitle = false;
          break;
      }

      if ($importanceTitle === false) {
        return false;
      }

      if (!empty($filePath)) {
        try {
          $logFilePath = self::LOG_DIR."/".$filePath;

          if (!is_file($logFilePath)) {
            if(!touch($logFilePath, "0777"))
              return false;
          }

          if(chmod($logFilePath, 0777) === false) 
            return false;
            
          $document = fopen($logFilePath, "ab+");

          $logEntryContent = "[".date("Y-m-d H:i:s")."] ".$importanceTitle." ".$content." \n";
          
          fputs($document, $logEntryContent);
          fclose($document);

          return true;

        } catch(\Throwable $e) {
          return false;
        }
      } else {
        return false;
      }
    }
  }
  