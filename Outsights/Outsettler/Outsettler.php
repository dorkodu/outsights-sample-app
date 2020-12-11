<?php
  namespace Outsights\Outsettler;

  use Outsights\Outstor\FileStorage;
  use Outsights\Utils\Json\JsonFile;
  use Outsights\Utils\Json\JsonPreprocessor;

  /**
   * a Configuration Library for Outsights
   */
	class Outsettler {

    private $currentEnvironment;
    
    public const STORE_PATH = "./outsights/resources/settings/";
    public const DEFAULT_ENVIRONMENT = "*";

    public $settings = array();

    public function __construct()
    {
      $this->currentEnvironment = self::DEFAULT_ENVIRONMENT;
      $this->resetEnvironment();
    }

    /**
     * Gets an entry from current environment settings
     *
     * @param string $key
     * 
     * @return string value for the key
     * @return null if couldn't get a key
     */
    public function get(string $key)
    {
      if (!empty($key)) {
        return $this->settings[$this->currentEnvironment][$key];
      } else return null;
    }

    /**
     * Sets a setting entry into current environment's settings
     *
     * @param string $key
     * @param string $value
     * 
     * @return void
     */
    public function set(string $key, string $value)
    {
      if (!empty($key) && !empty($value)) {
        $this->settings[$this->currentEnvironment][$key] = $value;
      } else {
        return false;
      }
    }

    /**
     * Generates the current environment's setting array
     *
     * @return array generated array
     */
    private function generateEnvironmentSettingsArray()
    {
      if (!empty($this->settings[$this->currentEnvironment]) && is_array($this->settings[$this->currentEnvironment])) {
        return array($this->currentEnvironment => $this->settings[$this->currentEnvironment]);
      } else {
        return array($this->currentEnvironment => array());
      }
    }
    
    /**
     * Saves the current environment into a JSON file
     * 
     * @return void
     */
    public function saveEnvironment()
    {
      $environmentSettings = $this->generateEnvironmentSettingsArray();
      $environmentFilePath = self::STORE_PATH . $this->currentEnvironment . ".json";
      
      $environmentJson = new JsonFile($environmentFilePath);

      return (!$environmentJson->write($environmentSettings, true)) ? false : true;
    }

    /**
     * Resets environment, and its contents
     *
     * @return void
     */
    public function resetEnvironment()
    {
      unset($this->settings[$this->currentEnvironment]);
    }

    /**
     * Refreshes the current environment from stored JSON file
     *
     * @return void
     */
    public function refreshEnvironment()
    {
      $environmentFilePath = self::STORE_PATH . $this->currentEnvironment . ".json";
      $environmentJson = new JsonFile($environmentFilePath);

      if($environmentJson->isUseful()) {
        $jsonContent = $environmentJson->read();
        $parsedSettings = JsonPreprocessor::parseJson($jsonContent);
        if ($parsedSettings === false) {
          $this->resetEnvironment();
        } else {
          
          unset($this->settings[$this->currentEnvironment]);

          $this->settings[$this->currentEnvironment] = $parsedSettings[$this->currentEnvironment];
        }
      } else {
        $this->resetEnvironment();
      }
    }

    /**
     * Switches between environments.
     *
     * @param string $environmentName Pattern should be : [a-zA-Z0-9-_]
     * 
     * @return void
     */
    public function switchEnvironment(string $environmentName)
    {
      if (preg_match("/^([a-zA-Z0-9_-]+)$/", $environmentName)) {
        $this->currentEnvironment = $environmentName;

        if (!array_key_exists($this->currentEnvironment, $this->settings)) {
          $this->refreshEnvironment();
        }
        return true;
      } else return false;
    }

    /**
     * Returns the current environment
     *
     * @return void
     */
    public function getEnvironment()
    {
			return $this->currentEnvironment;
    }
	}