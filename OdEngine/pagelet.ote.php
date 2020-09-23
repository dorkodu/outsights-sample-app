<?php
	namespace Outsights\OutsightsTemplateEngine;

	class Pagelet {
		protected $pageletName;
		protected $pageletLocation;
		protected $content;

		public function __construct() {

		}

		public function getPageletName() {
			if(empty($this->pageletName))
				return false;
			else
				return $this->pageletName;
		}

		public function setPageletName($pName) {
			if(empty($pName))
				return false;
			else {
				$this->pageletName = $pName;
				$this->pageletLocation = OTE_DIR."/pagelets/".strtolower($this->pageletName).'.pagelet';
			}

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
