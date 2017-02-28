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
	 * @throws PathNotFoundException
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
						$tblPathParameters = array();
						foreach($tblParameters as $i=>$item) {
							if($i==0) continue;
							$tblPathParameters[]=$item[0];
						}
						$this->request->setAttribute("path_parameters", $tblPathParameters);
						return $objRoute->getPath();
					}
				}
			}
			throw new PathNotFoundException("Route could not be matched to routes.route tag @ XML: ".$strURL);
		}
		
		return $strURL;
	}
	
	/**
	 * Detects page extension requested by client (or implies by default based on DD).
	 *
	 * @throws FormatNotFoundException
	 * @return string
	 */
	private function getValidPageExtension() {
		$strExtension = $this->request->getURI()->getPageExtension();
		
		// replace extension with default if not set
		if($strExtension=="") {
			$strExtension = $this->application->getDefaultExtension();
		}
		
		// validate extension
		if(!$this->application->hasFormat($strExtension)) throw new FormatNotFoundException("Extension could not be matched to formats.format tag @ XML: ".$strExtension);
		
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
