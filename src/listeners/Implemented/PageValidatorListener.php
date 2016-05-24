<?php
require_once("PathParameterFinder.php");

/**
 * Validates page request according to url, extension and content type
 */
class PageValidatorListener extends RequestListener {
	public function run() {
		$this->objRequest->setAttribute("page_url", $this->getValidPageURL());
		$this->objRequest->setAttribute("page_extension", $this->getValidPageExtension());
		$this->objRequest->setAttribute("page_content_type", $this->getValidPageContentType());
	}
	
	/**
	 * Detects page URL requested by client (or implies by default based on DD). When necessary, extracts path parameters from it.
	 *
	 * @throws ServletException
	 * @return string
	 */
	private function getValidPageURL() {
		$strURL = $this->objRequest->getURI()->getPagePath();
		
		// replace extension with default if not set
		if($strURL=="") {
			$strURL = $this->objApplication->getDefaultPage();
		}
		
		// validate page url
		if(!$this->objApplication->getAutoRouting()) {
			// normal url
			if($this->objApplication->hasRoute($strURL)) return $strURL;
			
			// search for path parameters
			$tblRoutes = $this->objApplication->getRoutes();
			foreach($tblRoutes as $objRoute) {
				if(strpos($objRoute->getPath(), "{")!==false) {
					$objPathParameterFinder = new PathParameterFinder($objRoute->getPath(), $strURL);
					if($objPathParameterFinder->isFound()) {
						$this->objRequest->setAttribute("path_parameters", $objPathParameterFinder->getParameters());
						return $objPathParameterFinder->getPath();
					}
				}
			}
			throw new ServletApplicationException("Route could not be located: ".$strURL);
		}
		
		return $strURL;
	}
	
	/**
	 * Detects page extension requested by client (or implies by default based on DD).
	 *
	 * @throws ServletException
	 * @return string
	 */
	private function getValidPageExtension() {
		$strExtension = $this->objRequest->getURI()->getPageExtension();
		
		// replace extension with default if not set
		if($strExtension=="") {
			$strExtension = $this->objApplication->getDefaultExtension();
		}
		
		// validate extension
		if(!$this->objApplication->hasFormat($strExtension)) throw new ServletApplicationException("Extension ".$strExtension." not defined @ formats.format!");
		
		return $strExtension;
	}
	
	/**
	 * Detects page content type requested by client (or implies by default based on DD).
	 *
	 * @throws ServletException
	 * @return string
	 */
	private function getValidPageContentType() {
		return $this->objApplication->getFormatInfo($this->objRequest->getAttribute("page_extension"))->getContentType();
	}
}