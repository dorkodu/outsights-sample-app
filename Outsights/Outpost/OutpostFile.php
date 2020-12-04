<?php
  namespace Outsights\Outpost;

  use Outsights\Outstor\AbstractFile;

  /**
   * Class to represent a "sent file" via HTTP POST.
   */
  class OutpostFile extends AbstractFile
  {
    private $inputName;
    private $tmpName;
    private $mimeType;

    /**
     * Class constructor.
     */
    public function __construct(string $name)
    {
      $this->inputName = $name;
      
      if ($this->isPresent()) {
        $fileArray = $_FILES[$this->inputName];

        if (in_array("name", $fileArray)) {
          $this->name = $fileArray["name"];
          $this->path = $fileArray["tmp_name"];
          $this->tmpName = $this->parseFileName($this->path);
          $this->size = $fileArray["size"];
          $this->mimeType = $fileArray["type"];
          $this->extension = $this->parseFileExtension($this->name);
        } 
      }
    }

    /**
     * Returns if a file is sent and present
     **/
    public function isPresent()
    {
      if (empty($this->inputName)) return false;

      if (isset($_FILES[$this->inputName]) && $_FILES[$this->inputName]['error'] == 0) {
        return true;
      } else {
        return false;
      }
    }

    /**
     * Returns the input name.
     **/
    public function inputName()
    {
      return $this->inputName;
    }

    /**
     * Returns the uploaded file's temporary name 
     **/
    public function tempName()
    {
      return $this->tmpName;
    }

    /**
     * Return the size of the uploaded file.
     **/
    public function size()
    {
      return $this->size;
    }
  }
  