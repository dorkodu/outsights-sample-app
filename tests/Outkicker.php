<?php

  /**
   *  A simple test library developed for writing better tests on Outsights ecosystem
   */
  class Outkicker
  {
    protected int $successCount = 0;
    protected int $failureCount = 0;

    /**
     * Class constructor.
     */
    public function __construct()
    {
      $this->successCount = 0;
      $this->failureCount = 0;
    }

    protected function commitFailure()
    {
      ++$this->failureCount;
      return false; # to return the value "false" directly 
    }

    protected function commitSuccess()
    {
      ++$this->successCount;
      return true; # to return the value "true" directly
    }

    public function getSuccessCount()
    {
      return $this->successCount;
    }

    public function getFailureCount()
    {
      return $this->failureCount;
    }

    /**
     * 
     *
     * @return void
     */
    public function getResultString()
    {
      return 
        (string) $this->successCount . " Success - " 
        . (string) $this->failureCount . " Failure";
    }

    /**
     * Check if this thing equals to your expectation.
     **/
    public function sayAreEqual($expectation, $parameterToTest)
    {
      if ($expectation !== $parameterToTest) 
        return $this->commitFailure(); 
      else 
        return $this->commitSuccess();
    }

    public function sayCount(int $expectedCount, $haystack)
    {
      if (count($haystack) !== $expectedCount)
        return $this->commitFailure(); 
      else
        return $this->commitSuccess();
    }

    public function sayContains(string $needle, string $haystack)
    {
      if (strstr($haystack, $needle) === false) 
        return $this->commitFailure();
      else
        return $this->commitSuccess();
    }

    public function sayIsNull($proposedValue)
    {
      if (is_null($proposedValue))
        return $this->commitSuccess();
      else 
        return $this->commitFailure();
    }

    public function sayNotNull($proposedValue)
    {
      if (!is_null($proposedValue))
        return $this->commitSuccess();
      else
        return $this->commitFailure();
    }

    public function sayArrayHasKey($key, array $haystack)
    {
      if (array_key_exists($key, $haystack))
        return $this->commitSuccess();
      else
        return $this->commitFailure();
    }

    public function sayEmpty($thing)
    {
      if(empty($thing))
        $this->commitSuccess();
      else
        $this->commitFailure();
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

    public function sayArrayStrictEquals($arrayToCompare, $objectYouHave)
    {
      
    }

    public function sayDirectoryExists($path)
    {
      if (is_dir($path))
        return $this->commitSuccess();
      else
        return $this->commitFailure();
    }

    public function sayFileExists($path)
    {
      if (is_file($path))
        return $this->commitSuccess();
      else
        return $this->commitFailure();
    }


  }