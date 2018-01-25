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
	private $strPath, $strControllerFile, $strViewFile, $strFormat;
	
	/**
	 * @param string $strPath
	 * @param string $strControllerFile
     * @param string $strViewFile
     * @param string $strFormat
	 */
	public function __construct($strPath, $strControllerFile, $strViewFile, $strFormat) {
		$this->strPath = $strPath;
		$this->strControllerFile = $strControllerFile;
		$this->strViewFile = $strViewFile;
        $this->strFormat = $strFormat;
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

    /**
     * Gets response format.
     *
     * @return string
     * @example json
     */
    public function getFormat() {
        return $this->strFormat;
    }
}