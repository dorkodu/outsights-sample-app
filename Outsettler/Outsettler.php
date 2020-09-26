<?php
  namespace Outsights\Outsettler;

  use Outsights\Outstor\FileStorage;
  use Outsights\Utils\Json\JsonFile;
  use Outsights\Utils\Json\JsonPreprocessor;

  /**
   * a Configuration library for Outsights
   */
	class Outsettler {

    private $currentEnvironment;
    
    public const STORE_PATH = "Outsettler/store/";
    public const DEFAULT_ENVIRONMENT = "default";

    private $settings = array();

    public function __construct()
    {
      $this->currentEnvironment = "DEFAULT";
      $this->resetEnvironment();
    }

    /**
     * Gets an entry from current environment settings
     *
     * @param string $key
     * 
     * @return mixed value for the key
     * @return null if couldn't get a key
     */
    public function get(string $key)
    {
      if (!empty($key) && !empty($this->settings[$this->currentEnvironment]) && !empty($this->settings[$this->currentEnvironment][$key])) {
        return $this->settings[$this->environment][$key];
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
      $environmentFilePath = self::STORE_PATH . mb_strtolower($this->currentEnvironment, "utf-8") . ".json";
      
      $environmentJson = new JsonFile($environmentFilePath);
      if(!$environmentJson->write($environmentSettings, true)) {
        return false;
      } else return true;
    }

    /**
     * Resets environment, and its contents
     *
     * @return void
     */
    public function resetEnvironment()
    {
      $this->settings[$this->currentEnvironment] = array();
    }

    /**
     * Refreshes the current environment from file storage
     *
     * @return void
     */
    public function refreshEnvironment()
    {
      $environmentFilePath = self::STORE_PATH . mb_strtolower($this->currentEnvironment, "utf-8") . ".json";
      $environmentJson = new JsonFile($environmentFilePath);

      if($environmentJson->isUseful()) {
        $jsonContent = $environmentJson->read();
        $parsedSettings = JsonPreprocessor::parseJson($jsonContent);
        if ($parsedSettings === false) {
          $this->resetEnvironment();
        } else {
          $this->settings[$this->currentEnvironment] = $parsedSettings;
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
    public function switchEnvironment($environmentName)
    {
      if (preg_match("/^([a-zA-Z0-9_-]+)$/", $environmentName)) {
        $this->currentEnvironment = $environmentName;
        $this->resetEnvironment();

        if (!array_key_exists($this->currentEnvironment, $this->settings) || !is_array($this->settings[$this->currentEnvironment])) {
          $this->refreshEnvironment();
        }
        return true;
      } else {
        return false;
      }
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