<?php 

  namespace Outsights\Outstor;
  
  /**
   * File class for Outstor.
   */
  class File extends AbstractFile
  {
    /**
     * Class constructor.
     */
    public function __construct(string $path)
    {
      $this->path = realpath($path);
      $this->name = $this->parseFileName($this->path);
      $this->extension = $this->parseFileExtension($this->name);
    }

    /**
     * Undocumented function.
     *
     * @param  $ description.
     *
     * @return type
     **/
    public function size()
    {
      return filesize($this->path);
    }
  }
  