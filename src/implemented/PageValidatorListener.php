<?php
/**
 * Validates page request according to url, extension and content type
 */
class PageValidatorListener extends RequestListener {
	public function run() {
		$this->request->setAttribute("page_url", $this->getValidPageURL());
		$this->request->setAttribute("page_extension", $this->getValidPageExtension());
		$this->request->setAttribute("page_content_type", $this->getValidPageContentType());
	}
	
	/**
	 * Detects page URL requested by client (or implies by default based on DD). When necessary, extracts path parameters from it.
	 *
	 * @throws ServletException
	 * @return string
	 */
	private function getValidPageURL() {
		$strURL = $this->request->getURI()->getPagePath();
		
		// replace extension with default if not set
		if($strURL=="") {
			$strURL = $this->application->getDefaultPage();
		}
		
		// validate page url
		if(!$this->application->getAutoRouting()) {
			// normal url
			if($this->application->hasRoute($strURL)) return $strURL;
			
			// search for path parameters
			$tblRoutes = $this->application->getRoutes();
			foreach($tblRoutes as $objRoute) {
				if(strpos($objRoute->getPath(), "(")!==false) {
					$pattern = "/^".str_replace(array("/","(*)","(d)","(w)"),array("\/","([^\/]+)","([0-9]+)","([a-zA-Z0-9]+)"),$objRoute->getPath())."$/";
					$tblParameters = array();
					if(preg_match_all($pattern, $strURL, $tblParameters)==1) {
						$this->request->setAttribute("path_parameters", $tblParameters[1]);
						return $objRoute->getPath();
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
		$strExtension = $this->request->getURI()->getPageExtension();
		
		// replace extension with default if not set
		if($strExtension=="") {
			$strExtension = $this->application->getDefaultExtension();
		}
		
		// validate extension
		if(!$this->application->hasFormat($strExtension)) throw new ServletApplicationException("Extension ".$strExtension." not defined @ formats.format!");
		
		return $strExtension;
	}
	
	/**
	 * Detects page content type requested by client (or implies by default based on DD).
	 *
	 * @throws ServletException
	 * @return string
	 */
	private function getValidPageContentType() {
		return $this->application->getFormatInfo($this->request->getAttribute("page_extension"))->getContentType();
	}
}
