<?php 
  namespace Outsights\PageWeaver;

  use Outsights\Outstor\FileStorage;

  /**
   * an Abstract Page class that file-based template views can implement
   */
  abstract class AbstractPage
  {
    protected const NAME_PATTERN = "/([a-zA-Z0-9-_]+)/";
    protected $name;
    protected $path;
		protected $contents;
    
    public function getName()
    {
      return $this->name;
		}

    public function getContents()
    {
      return $this->contents;
    }
    
    /**
     * Tells if name of the page is existing & suitable
     * Pattern is
     * 
     * @return boolean true on success, false on failure
     */
    public function isNameSuitable()
    {
      if (!empty($this->name)) {
        $result = preg_match(self::NAME_PATTERN, $this->name);
        switch ($result) {
          case 1:
            return true;
            break;
          default:
            return false;
            break;
        }
      } else return false;
    }

    public function setName($name)
    {
      if (preg_match(self::NAME_PATTERN, $name)) {
        $this->name = $name;
        $this->setPathByName($name);
      } else return false;
    }
    
    abstract public function setPathByName($name);

    /**
     * Checks if the page name is suitable and page file exists
     *
     * @return boolean
     */
		public function isUseful() {
      if ($this->isNameSuitable() && FileStorage::isUsefulFile($this->path)) {
        return true;
      } else return false;
		}

    /**
     * Reads the contents of the page.
     *
     * @return boolean true on success, false on failure
     */
    protected function readContents()
    {
			if($this->isUseful()) {
				$result = FileStorage::getContents($this->path);
        if ($result === false) {
          return false;
        } else {
          $this->contents = $result;
          return true; # success kid
        }
			} else return false; # file not useful
    }

    /**
     * Retrieves the pagelet, and returns its contents.
     *
     * @return string contents of the pagelet
     * @return false on failure
     * 
     */
    public function retrieve()
    {
      if ($this->isUseful()) {
        if($this->readContents()) {
          return $this->contents;
        } else return false;  
      } else return false;
		}
  }
  