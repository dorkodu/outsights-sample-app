<?php
  namespace Outkicker;

  /**
   * Say class provides useful assertions for Outkicker tests
   */
  class Say
  {
    /**
     * Check if this thing equals to your expectation.
     **/
    public static function equal($expectation, $parameterToTest)
    {
      if ($expectation !== $parameterToTest)
      throw new \Exception("SAY · Not Equal");
      
    }

    public static function count(int $expectedCount, $haystack)
    {
      if (count($haystack) !== $expectedCount)
      throw new \Exception("SAY : Count Does Not Match");
    }

    public static function contains(string $needle, string $haystack)
    {
      if (strpos($haystack, $needle) === false)
      throw new \Exception("SAY : Not Contains");
    }

    public static function null($proposedValue)
    {
      if(!is_null($proposedValue))
      throw new \Exception("SAY : Not Contains");
    }

    public static function notNull($proposedValue)
    {
      return !is_null($proposedValue);
    }

    public static function empty($thing)
    {
      return empty($thing);
    }

    public static function notEmpty($thing)
    {
      return !empty($thing);
    }

    public function arrayHasKey($key, array $haystack)
    {
      return array_key_exists($key, $haystack);
    }

    public function sayObjectEquals($objectToCompare, $objectYouHave)
    {
      
    }
    
    public function sayObjectStrictEquals($objectToCompare, $objectYouHave)
    {
      
    }

    public function sayArrayEquals(array $arrayToCompare, array $arrayYouHave)
    {
      if (count($arrayToCompare) === count($arrayYouHave) && array_diff($arrayToCompare, $arrayYouHave) === array_diff($arrayYouHave, $arrayToCompare)) {
        # code...
      }
    }

    public static function sayArrayStrictEquals($arrayToCompare, $objectYouHave)
    {
      
    }

    public static function directoryExists(string $path)
    {
      return is_dir($path);
    }

    public static function fileExists($path)
    {
      return is_file($path);
    }
  }
