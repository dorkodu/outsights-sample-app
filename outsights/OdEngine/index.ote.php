<?php
	namespace Outsights\OutsightsTemplateEngine;

	require_once OTE_DIR."/page.ote.php";
	require_once OTE_DIR."/pagelet.ote.php";

	use Outsights\OutsightsTemplateEngine\Page as Page;
	use Outsights\OutsightsTemplateEngine\Pagelet as Pagelet;

	class OTEIndex {
		public function buildPage($pType, Array $pParams = array()) {
			$wantedPage = new Page();
			$wantedPage->setPageName($pType);
			$wantedPage->loadPage();
			$wantedPage->seedPagelets();
			$wantedPage->replacePlaceholders($pParams);
			return $wantedPage->getPageContent();
		}
	}