<?php
  namespace Outsights\Outpost;

  use Outsights\Outstor\AbstractFile;

  /**
   *  Class to represent a file which will be sent via a HTTP POST request.
   */
  class OutpostFile extends AbstractFile
  {
    private string $inputName;

    /**
     * Class constructor.
     */
    public function __construct(string $path, string $inputName, string $name = "")
    {
      $this->path = $path;
      
      if ($this->exists()) {
        /**
         * if a preferred name is given, will use it. 
         * otherwise, will parse from the path
         */
        $this->name = !empty($name) ? $name : $this->parseFileName($this->path);

        $this->size = filesize($this->path);
        $this->mimeType = mime_content_type($this->path);
        $this->extension = $this->parseFileExtension($this->name);
        $this->inputName = $inputName;
      }
    }
    
    /**
     * Returns if a file is sent and present
     **/
    public function exists()
    {
      return is_file($this->path);
    }

    /**
     * Returns the input name.
     **/
    public function inputName()
    {
      return $this->inputName;
    }

    /**
     * Return the size of the file.
     **/
    public function size()
    {
      return $this->size;
    }
  }
  