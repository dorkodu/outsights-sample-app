<?php 

  namespace Outsights\Outstor;
  
  /**
   * a Representational Abstract File Class for Outstor.
   */
  abstract class AbstractFile
  {
    protected $name;
    protected $path;
    protected $contents;
    protected $extension;

    /**
     * Class constructor.
     */
    public function __construct(string $path)
    {
      $this->path = $path;
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
        return null;
      } else {
        $tempPathElements = explode(".", $path);
        return strtolower(end($tempPathElements));
      }
    }

    public function name()
    {
      return $this->name;
    }

    public function path()
    {
      return $this->path;
    }

    public function extension()
    {
      return $this->extension;
    }

    public function mimeType()
    {
      return mime_content_type($this->path);
    }

    public function contents() 
    {
			return $this->contents;
    }

    abstract public function size();
    
    public function isUseful()
    {
      return (is_file($this->path) && is_readable($this->path) && is_writeable($this->path));
    }
		
    public function read() 
    {
			$contents = file_get_contents($this->path);
			if($contents !== null || $contents !== false) {
        $this->contents = $contents;
        return $contents;
			} else return false; # problem with reading the file
    }

    /** 
     * Writes to a file.
     * 
		 * @param string $contents Writes contents into the file
     */
    public function write(string $contents) 
    {
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
     * Modify file properties only if content modified
     */
    protected function putContentsIfModified($path, $content)
    {
        $currentContent = file_get_contents($path);
        if (!$currentContent || ($currentContent != $content)) {
            return file_put_contents($path, $content);
        }
        return 0;
    }
  }
  