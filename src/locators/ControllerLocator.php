<?php
/**
 * Locates controller based on page requested by client and values set in DD.
 */
final class ControllerLocator {
	private $strClassName;
	
	/**
	 * @param Application $objApplication
	 * @param string $strPagePath
	 */
	public function __construct(Application $objApplication, $strPagePath) {
		$this->setClassName($objApplication, $strPagePath);
	}

	/**
	 * Sets controller class name. 
	 * 
	 * @param Application $objApplication
	 * @param string $strPagePath
	 * @throws ServletException
	 */
	private function setClassName(Application $objApplication, $strPagePath) {
		// get controller class folder
		$strFolder = $objApplication->getControllersPath();
	
		// gets page url
		$strURL = $strPagePath;
	
		// get controller class name
		$strFile = "";
		$strClass = "";
		if(!$objApplication->getAutoRouting()) {
			$strPath = $objApplication->getRouteInfo($strURL)->getController();
			if(!$strPath) return;
			$strFile = $strFolder."/".$strPath.".php";
			$slashPosition = strrpos($strPath,"/");
			if($slashPosition!==false) {
				$strClass = substr($strPath,$slashPosition+1);
				if(!$strClass) throw new ServletException("Invalid controller set for route: ".$strURL);
			} else {
				$strClass = $strPath;
			}
		} else {
			$strClass = str_replace(" ","",ucwords(str_replace(array("/","-")," ",strtolower($strURL))))."Controller";
			$strFile = $strFolder."/".$strClass.".php";
		}
		
		// loads controller file
		if(!file_exists($strFile)) throw new ServletException("Controller not found: ".$strClass);
		require_once($strFile);

		// validates and sets controller class
		if(!class_exists($strClass)) throw new ServletException("Controller class not found: ".$strClass);
		if(!is_subclass_of($strClass, "Controller")) throw new ServletException($strClass." must be a subclass of Controller");
		$this->strClassName = $strClass;
	}

	/**
	 * Gets controller class name.
	 *
	 * @return string
	 */
	public function getClassName() {
		return $this->strClassName;
	}
}
