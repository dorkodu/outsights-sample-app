<?php

/**
 * Provides Assertions
 **/
class Assert
{
    public static function AreEqual( $a, $b )
    {
        if ( $a != $b )
        {
            throw new Exception( 'Subjects are not equal.' );
        }
    }
}

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


    public function getSuccess()
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
        return $this->ParseComment( $this->_test->getDocComment() );
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

    public static function createFailure( Testable $object, ReflectionMethod $test, Exception $exception )
    {
        $result = new self();
        $result->_isSuccess = false;
        $result->testableInstance = $object;
        $result->_test = $test;
        $result->_exception = $exception;

        return $result;
    }

    public static function createSuccess(Testable $object, ReflectionMethod $test)
    {
        $result = new self();
        $result->_isSuccess = true;
        $result->testableInstance = $object;
        $result->_test = $test;

        return $result;
    }
}

/**
 * Provides a base class to derive tests from
 **/
abstract class Testable
{
    protected $test_log = array();

    /**
     * Logs the result of a test. keeps track of results for later inspection, Overridable to log elsewhere.
     **/
    protected function log( TestResult $result )
    {
        $this->test_log[] = $result;

        printf( "Test: %s was a %s %s\n"
            ,$result->getName()
            ,$result->getSuccess() ? 'success' : 'failure'
            ,$result->getSuccess() ? '' : sprintf( "\n%s (lines:%d-%d; file:%s)"
                ,$result->getComment()
                ,$result->getTest()->getStartLine()
                ,$result->getTest()->getEndLine()
                ,$result->getTest()->getFileName()
                )
            );

    }

    public final function RunTests()
    {
        $class = new ReflectionClass( $this );
        foreach( $class->GetMethods() as $method )
        {
            $methodname = $method->getName();
            if ( strlen( $methodname ) > 4 && substr( $methodname, 0, 4 ) == 'Test' )
            {
                ob_start();
                try
                {
                    $this->$methodname();
                    $result = TestResult::CreateSuccess( $this, $method );
                }
                catch( Exception $ex )
                {
                    $result = TestResult::CreateFailure( $this, $method, $ex );
                }
                $output = ob_get_clean();
                $result->setOutput( $output );
                $this->Log( $result );
            }
        }
    }
}

/**
 * a simple Test suite with two tests
 **/
class MyTest extends Testable
{
    /**
     * This test is designed to fail
     **/
    public function TestOne()
    {
        Assert::AreEqual( 1, 2 );
    }

    /**
     * This test is designed to succeed
     **/
    public function TestTwo()
    {
        Assert::AreEqual( 1, 1 );
    }
}

// this is how to use it.
$test = new MyTest();
$test->RunTests();