<?php
  namespace Outkicker;

  use Outkicker\TestResult;

  /**
   *  A simple test library developed for writing better tests on Outsights ecosystem
   *  Outkicker provides a testable interface for a class
   */
  abstract class Outkicker
  {
    protected $testClassName = "";
    protected $testLog = array();

    /**
     * Logs the result of a test. keeps track of results for later inspection, Overridable to log elsewhere.
     **/
    protected final function logTest(TestResult $result)
    {
      $this->testLog[] = $result;

      printf( "Outkicker > %s.%s was a %s %s %s\n"
        ,$this->testClassName
        ,$result->getName()
        ,$result->isSuccess() ? 'SUCCESS' : 'FAILURE'
        ,$result->isSuccess() ? '' : sprintf( "\nException : %s"
            ,$result->getException()->getMessage()
            )
        ,$result->isSuccess() ? '' : sprintf( "\nComment : \n%s \n(Lines: %d-%d ~ File: %s)\n"
            ,$result->getComment()
            ,$result->getTest()->getStartLine()
            ,$result->getTest()->getEndLine()
            ,$result->getTest()->getFileName()
            )
        );
    }

    public final function runTests()
    {
      $class = new \ReflectionClass( $this );
      $this->testClassName = $class->getName();

      foreach( $class->getMethods() as $method )
      {
        $methodname = $method->getName();
        if ( strlen( $methodname ) > 4 && substr( $methodname, 0, 4 ) == 'test' )
        {
          ob_start();
          # started output buffering
          try {
            $this->$methodname();
            $result = TestResult::createSuccess( $this, $method );
          } catch( \Exception $ex ) {
            $result = TestResult::createFailure( $this, $method, $ex );
          }

          $output = ob_get_clean();
          $result->setOutput( $output );
          $this->logTest( $result );
        }
      }

      $this->logSummary();
    }

    public final function logSummary()
    {
      $successCount = 0;
      $failureCount = 0;

      foreach ($this->testLog as $logEntry) {
        if ($logEntry->isSuccess()) 
          ++$successCount;
        else
          ++$failureCount;
      }

      printf( "Outkicker > SUMMARY %s : %d Success %d Failed\n"
            ,$this->testClassName
            ,$successCount
            ,$failureCount
          );

    }
  }