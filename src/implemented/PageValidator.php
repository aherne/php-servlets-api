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
		$extension = $application->getDefaultExtension();
		$position = strrpos($page, ".");
		if($position) {
			$pageExtension = substr($page,$position+1);
			if($application->hasFormat($pageExtension)) {
				$extension = $pageExtension;
				$page = substr($page,0,-strlen($pageExtension)-1);
			}
		}
		
		// set values
		$this->setContentType($application, $extension);
		$this->setPage($application, $page);
	}
	
	/**
	 * Sets requested content type
	 * 
	 * @param Application $application
	 * @param string $extension
	 */
	private function setContentType(Application $application, $extension) {
		$format = $application->getFormatInfo($extension);
		$this->strContentType = $format->getContentType().($format->getCharacterEncoding()?"; charset=".$format->getCharacterEncoding():"");
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
	 * Sets requested page & path parameters.
	 * 
	 * @param Application $application
	 * @param string $strURL
	 * @throws PathNotFoundException
	 */
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
						preg_match_all("/(\(([^)]+)\))/", $objRoute->getPath(), $matches);
						$names = $matches[2];
						$pattern = "/^".str_replace($matches[1],"([^\/]+)",str_replace("/","\/",$objRoute->getPath()))."$/";
						if(preg_match_all($pattern,$strURL,$results)==1) {
							foreach($results as $i=>$item) {
								if($i==0) continue;
								$this->tblPathParameters[$names[$i-1]]=$item[0];
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
	 * Gets requested validated page
	 * 
	 * @return string
	 */
	public function getPage() {
		return $this->strPage;
	}
	
	/**
	 * Gets value of path parameter
	 *
	 * @param string $name
	 * @return string|null Null if parameter doesn't exist, string otherwise.
	 */
	public function getPathParameter($name) {
		return (isset($this->tblPathParameters[$name])?$this->tblPathParameters[$name]:null);
	}
	
	/**
	 * Gets all path parameters
	 *
	 * @return array[string:string]
	 */
	public function getPathParameters() {
		return $this->tblPathParameters;
	}
}