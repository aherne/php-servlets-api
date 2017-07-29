<?php
/**
 * Single responsibility in validating requested page based on configuration.xml encapsulated by Application object
 */
class PageValidator implements RequestValidator {
	private $strPage;
	private $strContentType;
	private $tblPathParameters=array();
	
	/**
	 * @param string $page 
	 * @param Application $application
	 * @throws PathNotFoundException
	 */
	public function __construct($page, Application $application) {
		// split page into extension & page
		$stripExtension = false;
		$extension = $application->getDefaultExtension();
		$position = strrpos($page, ".");
		if($position) {
			$pageExtension = substr($page,$position+1);
			if($application->hasFormat($pageExtension)) {
				$extension = $pageExtension;
				$stripExtension = true;
			}
		}
		
		// set values
		$this->setContentType($application, $extension);
		$this->setPage($application, (!$stripExtension?$page:substr($page,0,-strlen($extension)-1)));
	}
	
	private function setContentType(Application $application, $extension) {
		$format = $application->getFormatInfo($extension);
		$this->strContentType = $format->getContentType().($format->getCharacterEncoding()?"; charset=".$format->getCharacterEncoding():"");
	}
	
	private function setPage(Application $application, $strURL) {
		if($strURL=="") {
			$strURL = $application->getDefaultPage();
		}
		if(!$application->getAutoRouting()) {
			if(!$application->hasRoute($strURL)) {
				$blnMatchFound = false;
				$tblRoutes = $application->getRoutes();
				foreach($tblRoutes as $objRoute) {
					if(strpos($objRoute->getPath(), "(")!==false) {
						$pattern = "/^".str_replace(array("/","(*)","(d)","(w)"),array("\/","([^\/]+)","([0-9]+)","([a-zA-Z0-9]+)"),$objRoute->getPath())."$/";
						$tblParameters = array();
						if(preg_match_all($pattern, $strURL, $tblParameters)==1) {
							foreach($tblParameters as $i=>$item) {
								if($i==0) continue;
								$this->tblPathParameters[]=$item[0];
							}
							$strURL = $objRoute->getPath();
							$blnMatchFound = true;
							break;
						}
					}
				}
				if(!$blnMatchFound) throw new PathNotFoundException("Route could not be matched to routes.route tag @ XML: ".$strURL);
			}
		}
		$this->strPage = $strURL;
	}
	
	/**
	 * Gets requested content type.
	 * 
	 * @return string
	 */
	public function getContentType() {
		return $this->strContentType;
	}
	
	/**
	 * Gets requested validated page
	 * 
	 * @return string
	 */
	public function getPage() {
		return $this->strPage;
	}
	
	/**
	 * Gets path parameters, if any
	 * 
	 * @return array 
	 */
	public function getPathParameters() {
		return $this->tblPathParameters;
	}
}