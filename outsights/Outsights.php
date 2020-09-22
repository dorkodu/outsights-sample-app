<?php
  namespace Outsights;
  
  /**
   * Outsights application class
   */
  class Outsights
  {
    /**
     * Class constructor.
     */
    public function __construct()
    {
 
    }

    /**
     * Runs the Outsights application.
     * This is the only way to run the 'application tied to framework'. 
     * The whole Outsights framework can be plugged-out from your application.
     * 
     **/
    public function run()
    {
      require_once "Router/web.php";
    }
  }
  