<?php
	namespace Outsights\PageWeaver;

	class Pagelet {
		protected $name;
		protected $content;

		public function __construct(string $name) {
      $this->name = $name;
		}

		public function getName() {
      return $this->name;
		}

		public function setName(string $name) {
      $this->pageletName = $name;
		}

		public function isPageletExists() {
			if(file_exists($this->pageletLocation))
				return true;
			else
				return false;
		}

		public function isPageletReadable() {
			if(is_readable($this->pageletLocation))
				return true;
			else
				return false;
		}

		protected function loadPagelet() {
			if($this->isPageletExists() && $this->isPageletReadable()) {
				$this->content = file_get_contents($this->pageletLocation);
				return true;
			} else
				return false;
		}

		public function retrievePagelet() {
			if(!empty($this->pageletName)) {
				if($this->loadPagelet())
					return $this->content;
				else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
