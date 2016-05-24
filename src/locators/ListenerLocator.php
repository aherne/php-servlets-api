<?php
/**
 * Locates listener based on component name.
 */
final class ListenerLocator {
	private $tblClassNames = array();
	
	public function __construct($objApplication) {
		$this->setClassNames($objApplication);
	}

	/**
	 * Locates listeners by component name (configuration | request | response).
	 *
	 * @throws ServletException
	 */
	private function setClassNames(Application $objApplication) {
		$strListenerPath = $objApplication->getListenersPath();
		$tblListeners = $objApplication->getListeners();
		
		// gets classes
		$tblOutput = array();
		foreach($tblListeners as $strClassName) {
			// load file
			$strFile = $strListenerPath."/".$strClassName.".php";
			if(!file_exists($strFile)) throw new ServletException("Listener file not found: ".$strFile);
			require_once($strFile);
				
			// verify class
			if(!class_exists($strClassName)) throw new ServletException("Listener class not found: ".$strClassName);
			$tblOutput[] = $strClassName;
		}
		
		$this->tblClassNames = $tblOutput;
	}
	
	/**
	 * Gets class names by parent class name.
	 * 
	 * @param string $strParentClassName
	 * @return string[]
	 */
	public function getClassNames($strParentClassName) {
		$tblOutput = array();
		foreach($this->tblClassNames as $strClassName) {
			if(is_subclass_of($strClassName, $strParentClassName)) {
				$tblOutput[] = $strClassName;
			}
		}
		return $tblOutput;
	}
}