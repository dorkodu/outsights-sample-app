<?php
  namespace Outsights\PageWeaver;

  use Outsights\PageWeaver\AbstractPage;
  use Outsights\PageWeaver\Pagelet;
  use Outsights\Outstor\FileStorage;

	class Page extends AbstractPage {

    private const PAGES_DIR = "pages/";
    private const PAGELET_PLACEHOLDER_PATTERN = "/{([a-zA-Z0-9-_]+).pagelet}/";

		public function __construct(string $name) {
      if (preg_match(self::NAME_PATTERN, $name)) {
        $this->name = $name;
        $this->setPathByName($name);
      }
		}

    public function setPathByName($name)
    {
      $this->path = self::PAGES_DIR.$this->name.".page";
    }
    
    /**
     * Replaces placeholders with given data
     * 
     * @param array $placeholders
     * @return void
     */
		public function seedData(array $placeholders) {
			foreach($placeholders as $key => $value) {
				$this->contents = str_replace('{'.$key.'}', $value, $this->contents);
			}
		}

		/**
     * Tells if there is any pagelet placeholders exist
     *
     * @return boolean
     */
		protected function isThereAnyPagelets() {
      if(!empty($this->content)) {
        $result = preg_match(self::PAGELET_PLACEHOLDER_PATTERN, $this->content);
        switch ($result) {
          case 1:
            return true;
          default:
            return false;
            break;
        }  
      } else return false;
		}

		/**
     * Seeds the pagelets.
     *
     * @return void
     */
		public function seedPagelets() {
			while($this->isThereAnyPagelets()) {
				preg_match_all(self::PAGELET_PLACEHOLDER_PATTERN, $this->content, $results);
				$tokensArray = $results[0];
				unset($results);
				foreach($tokensArray as $token) {
					preg_match_all(self::PAGELET_PLACEHOLDER_PATTERN, $token, $results);
					$pagelet = new Pagelet($results[1][0]);
					unset($results);
					$this->content = str_replace($token, $pagelet->retrieve(), $this->content);
				}
      }
		}
	}
