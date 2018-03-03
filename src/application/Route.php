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
	private $path, $controllerFile, $viewFile, $format;
	
	/**
	 * @param string $path
	 * @param string $controllerFile
     * @param string $viewFile
     * @param string $format
	 */
	public function __construct($path, $controllerFile, $viewFile, $format) {
		$this->path = $path;
		$this->controllerFile = $controllerFile;
		$this->viewFile = $viewFile;
        $this->format = $format;
	}
	
	/**
	 * Gets route path.
	 * 
	 * @return string
	 * @example test/mine		without path parameters		
	 * @example test/{a}/{b}	with path parameters
	 */
	public function getPath() {
		return $this->path;
	}
	
	/**
	 * Gets controller name.
	 * 
	 * @return string
	 * @example TestController
	 */
	public function getController() {
		return $this->controllerFile;
	}
	
	/**
	 * Gets view path.
	 * 
	 * @return string
	 * @example asd/fgh.html
	 */
	public function getView() {
		return $this->viewFile;
	}

    /**
     * Gets response format.
     *
     * @return string
     * @example json
     */
    public function getFormat() {
        return $this->format;
    }
}