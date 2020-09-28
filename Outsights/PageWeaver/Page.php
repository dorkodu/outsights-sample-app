<?php
  namespace Outsights\PageWeaver;

  use Outsights\PageWeaver\AbstractPage;
  use Outsights\PageWeaver\Pagelet;
  use Outsights\Outstor\FileStorage;

  class Page extends AbstractPage
  {

    private const PAGES_DIR = "PageWeaver/pages/";
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
      if(!empty($this->contents)) {
        $result = preg_match(self::PAGELET_PLACEHOLDER_PATTERN, $this->contents);
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
				preg_match_all(self::PAGELET_PLACEHOLDER_PATTERN, $this->contents, $resultsToken);
        $tokensArray = $resultsToken[0];

        foreach($tokensArray as $token) {
					preg_match_all(self::PAGELET_PLACEHOLDER_PATTERN, $token, $results);
          
          $pageletName = $results[1][0];

          $pagelet = new Pagelet($pageletName);
          $pageletContents = $pagelet->retrieve();

          $this->contents = str_replace($token, $pageletContents, $this->contents);
          
          unset($pageletContents);
          unset($pageletName);
          unset($pagelet);
        }
      }
		}
	}
