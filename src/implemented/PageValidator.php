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
     * @throws FormatNotFoundException
	 */
	public function __construct($page, Application $application) {
        $this->validate($application, $page);
	}
	
	/**
	 * Detects requested page, format & path parameters by matching routes/formats in xml to requested route.
	 * 
	 * @param Application $application
	 * @param string $strURL
	 * @throws PathNotFoundException
     * @throws FormatNotFoundException
	 */
	private function validate(Application $application, $strURL) {
		if($strURL=="") {
			$strURL = $application->getDefaultPage();
		}
		$extension = $application->getDefaultExtension();
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
							if($objRoute->getFormat()) {
							    $extension = $objRoute->getFormat();
                            }
							$strURL = $objRoute->getPath();
							$blnMatchFound = true;
							break;
						}
					}
				}
				if(!$blnMatchFound) throw new PathNotFoundException("Route could not be matched to routes.route tag @ XML: ".$strURL);
			} else {
                $objRoute = $application->getRouteInfo($strURL);
			    if($objRoute->getFormat()) {
                    $extension = $objRoute->getFormat();
                }
            }
		}
		$this->strPage = $strURL;

        $format = $application->getFormatInfo($extension);
        $this->strContentType = $format->getContentType().($format->getCharacterEncoding()?"; charset=".$format->getCharacterEncoding():"");
	}
	
	/**
	 * {@inheritDoc}
	 * @see RequestValidator::getPage()
	 */
	public function getPage() {
		return $this->strPage;
	}
	
	/**
	 * {@inheritDoc}
	 * @see RequestValidator::getPathParameter()
	 */
	public function getPathParameter($name) {
		return (isset($this->tblPathParameters[$name])?$this->tblPathParameters[$name]:null);
	}
	
	/**
	 * {@inheritDoc}
	 * @see RequestValidator::getPathParameters()
	 */
	public function getPathParameters() {
		return $this->tblPathParameters;
	}

    /**
     * {@inheritDoc}
     * @see RequestValidator::getPathParameters()
     */
    public function getContentType() {
        return $this->strContentType;
    }
}
