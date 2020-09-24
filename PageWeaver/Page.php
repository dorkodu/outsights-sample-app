<?php
  namespace Outsights\PageWeaver;
  
	use Outsights\PageWeaver\Pagelet;

	class Page {

		protected $pageName;
		protected $pageLocation;
		protected $content;

		public function __construct() {

		}

		public function getPageName() {
			if(empty($this->pageName))
				return false;
			else
				return $this->pageName;
		}

		public function getPageContent() {
			if(empty($this->content))
				return false;
			else
				return $this->content;
		}

		public function setPageName($pName) {
			if(empty($pName))
				return false;
			else {
				$this->pageName = $pName;
				$this->pageLocation = "/page-templates/".strtolower($this->pageName).'.page';
			}
		}

		public function isPageExists() {
			if(file_exists($this->pageLocation))
				return true;
			else
				return false;
		}

		public function isPageReadable() {
			if(is_readable($this->pageLocation))
				return true;
			else
				return false;
		}

		public function loadPage() {
			if($this->isPageExists() && $this->isPageReadable()) {
				$this->content = file_get_contents($this->pageLocation);
				return true;
			} else
				return false;
		}

		public function replacePlaceholders(Array $placeholders) {
			if(empty($this->content)) {
					$this->loadPage();
			}
			foreach($placeholders as $pKey => $pValue) {
				$this->content = str_replace('{'.$pKey.'}', $pValue, $this->content);
			}
		}

		#recognize the pagelets in a page
		protected function isThereAnyPagelets() {
			if(!empty($this->content))
				return preg_match("/{(.+).pagelet}/", $this->content);
			else
				return false;
		}

		#if any pagelets there, retrieve them for the page.
		public function seedPagelets() {
			if($this->isThereAnyPagelets()) {
				preg_match_all("/{(.+).pagelet}/", $this->content, $results);
				$tokensArray = $results[0];
				unset($results);
				foreach($tokensArray as $token) {
					preg_match_all("/{(.+).pagelet}/", $token, $r);
					$pagelet = new Pagelet();
					$pagelet->setPageletName($r[1][0]);
					unset($r);
					$this->content = str_replace($token, $pagelet->retrievePagelet(), $this->content);
				}
			} else
				return false;
		}
	}
