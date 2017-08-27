<?php
/**
 * Encapsulates route information:
 * - url: relative path requested
 * - class: controller class name (by convention same as file name)
 * Utility @ Application class.
 * 
 * @author aherne
 */
class Route {
	private $strPath, $strControllerClass, $strViewFile;
	
	/**
	 * @param string $strPath
	 * @param string $strControllerClass
	 */
	public function __construct($strPath, $strControllerClass, $strViewFile) {
		$this->strPath = $strPath;
		$this->strControllerClass = $strControllerClass;
		$this->strViewFile = $strViewFile;
	}
	
	/**
	 * Gets route path.
	 * 
	 * @return string
	 * @example test/mine		without path parameters		
	 * @example test/{a}/{b}	with path parameters
	 */
	public function getPath() {
		return $this->strPath;
	}
	
	/**
	 * Gets controller name.
	 * 
	 * @return string
	 * @example TestController
	 */
	public function getController() {
		return $this->strControllerClass;
	}
	
	/**
	 * Gets view path.
	 * 
	 * @return string
	 * @example asd/fgh.html
	 */
	public function getView() {
		return $this->strViewFile;
	}
}