<?php
/**
 * Locates wrapper based on extension of page requested by client (or overridden by controller) and values set in DD.
 */
final class WrapperLocator {	
	const DEFAULT_WRAPPER = "ViewWrapper";
	private $strClassName;
	
	/**
	 * @param Application $objApplication
	 * @param string $strContentType
	 */
	public function __construct(Application $objApplication, $strContentType) {
		$this->setClassName($objApplication, $strContentType);
	}

	/**
	 * Gets wrapper class name.
	 *
	 * @param Application $objApplication
	 * @param string $strContentType
	 * @throws ServletException
	 */
	private function setClassName(Application $objApplication, $strContentType) {
		// get listener path
		$strWrapperClass = "";
		$strWrapperLocation = "";
		
		// detect wrapper @ application
		if($objApplication->getWrappersPath()) {
			$tblFormats = $objApplication->getFormats();			
			foreach($tblFormats as $objFormat) {
				$strWrapperClass = $objFormat->getWrapper();
				if(strpos($strContentType, $objFormat->getContentType()) === 0 && $strWrapperClass) {
					$strWrapperLocation = $objApplication->getWrappersPath()."/".$strWrapperClass.".php";
					if(!file_exists($strWrapperLocation)) throw new ServletException("Wrapper not found: ".$strWrapperLocation);
					require_once($strWrapperLocation);
					break;
				}
			}
		}
		
		// if no wrapper was defined, use ViewWrapper
		if(!$strWrapperLocation) $strWrapperClass = self::DEFAULT_WRAPPER;
		
		// validate wrapper found or use default
		if(!class_exists($strWrapperClass)) throw new ServletException("Wrapper class not defined: ".$strWrapperClass);
		
		$this->strClassName = $strWrapperClass;
		
		// checks if it is a subclass of Controller
		if(!is_subclass_of($this->strClassName, "Wrapper")) throw new ServletException($this->strClassName." must be a subclass of Wrapper");
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