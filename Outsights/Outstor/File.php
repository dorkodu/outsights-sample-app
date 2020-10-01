<?php 

  namespace Outsights\Outstor;
  
  /**
   * a representational File class for Outstor.
   */
  class File
  {
    protected $name;
    protected $path;
    protected $contents;
    protected $type = null;
    protected $size;
    protected $extension;

    /**
     * Class constructor.
     */
    public function __construct(string $path)
    {
      $this->path = $path;
      $this->name = $this->parseFileName($path);
      $this->extension = $this->parseFileExtension($this->name);
    }

    protected function parseFileName($path)
    {
      if (strpos($path, DIRECTORY_SEPARATOR) === false) {
        return $path;
      } else {
        $tempPathElements = explode(DIRECTORY_SEPARATOR, $path);
        return end($tempPathElements);
      }
    }

    protected function parseFileExtension($path)
    {
      if (strpos($path, ".") === false) {
        return $path;
      } else {
        $tempPathElements = explode(".", $path);
        return strtolower(end($tempPathElements));
      }
    }

    public function getName()
    {
			return $this->name;
    }

    public function getPath()
    {
			return $this->path;
    }

    public function getExtension()
    {
      return $this->extension;
    }

    public function getContents() {
			return $this->contents;
    }

    public function getSize()
    {
      return filesize($this->path);
    }

    public function isUseful() {
      return (is_file($this->path) && is_readable($this->path) && is_writeable($this->path));
    }
		
    public function read() {
			$contents = file_get_contents($this->path);
			if($contents !== null || $contents !== false) {
        $this->contents = $contents;
        return $contents;
			} else return false; # problem with reading the file
    }

    /** 
     * Writes to a file.
		 * @param string $contents Writes contents into the file
     */
    public function write(string $contents) {
			$dir = dirname($this->path);
			if (!is_dir($dir)) {
				if (file_exists($dir)) return false; # it exists and not a directory
				if (!@mkdir($dir, 0777, true)) return false; # it does not exists and could not be created
			}
			$retries = 3;
			while ($retries--) {
				try {
					$this->putContentsIfModified($this->path, $contents);
					break;
				} catch (\Exception $e) {
					if ($retries) {
						usleep(500000);
						continue;
					}
					throw $e;
				}
			}
    }

    /**
     * modify file properties only if content modified
     */
    private function putContentsIfModified($path, $content)
    {
        $currentContent = file_get_contents($path);
        if (!$currentContent || ($currentContent != $content)) {
            return file_put_contents($path, $content);
        }
        return 0;
    }
  }
  