<?php
  namespace Outkicker;

  /**
   * Provides a loggable entity with information on a test and how it executed
   **/
  class TestResult
  {
      protected $_testableInstance = null;
      protected $_isSuccess = false;
      protected $_output = '';
      protected $_test = null;
      protected $_exception = null;

      public function isSuccess()
      {
          return $this->_isSuccess;
      }
      
      public function getOutput()
      {
          return $this->_output;
      }

      public function setOutput( $value )
      {
          $this->_output = $value;
      }

      public function getTest()
      {
          return $this->_test;
      }

      public function getName()
      {
          return $this->_test->getName();
      }

      public function getComment()
      {
          return $this->parseComment( $this->_test->getDocComment() );
      }

      private function parseComment($comment)
      {
          $lines = explode( "\n", $comment );
          for( $i = 0; $i < count( $lines ); $i ++ )
          {
              $lines[$i] = trim( $lines[ $i ] );
          }
          return implode( "\n", $lines );
      }

      public function getException()
      {
          return $this->_exception;
      }

      public static function createFailure( Outkicker $object, \ReflectionMethod $test, \Exception $exception )
      {
          $result = new self();
          $result->_isSuccess = false;
          $result->testableInstance = $object;
          $result->_test = $test;
          $result->_exception = $exception;

          return $result;
      }

      public static function createSuccess( Outkicker $object, \ReflectionMethod $test )
      {
          $result = new self();
          $result->_isSuccess = true;
          $result->testableInstance = $object;
          $result->_test = $test;

          return $result;
      }
  }