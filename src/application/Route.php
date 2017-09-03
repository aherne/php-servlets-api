<?php
/**
 * Encapsulates route information:
 * - url: relative path requested
 * - cpntroller: path to controller (relative to application/controllers folder)
 * - view: path to view (relative to application/views folder)
 * 
 * Utility @ Application class.
 * 
 * @author aherne
 */
class Route {
	private $strPath, $strControllerFile, $strViewFile;
	
	/**
	 * @param string $strPath
	 * @param string $strControllerFile
	 */
	public function __construct($strPath, $strControllerFile, $strViewFile) {
		$this->strPath = $strPath;
		$this->strControllerFile = $strControllerFile;
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
		return $this->strControllerFile;
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