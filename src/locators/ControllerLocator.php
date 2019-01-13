<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Locates controller by matching page requested by client to data in XML tag "route".
 */
class ControllerLocator {
	private $className;
	
	/**
	 * Detects controller based on page requested and XML. 
	 * 
	 * @param Application $application
	 * @param string $pagePath
	 * @throws ServletException If controller file could not be located on disk.
	 * @throws XMLException If XML is misconfigured 
	 */
	public function __construct(Application $application, $pagePath) {
		$this->setClassName($application, $pagePath);
	}
	

	/**
	 * Sets controller class name. 
	 * 
	 * @param Application $application
	 * @param string $pagePath
	 * @throws ServletException If controller file could not be located on disk.
	 * @throws XMLException If XML is misconfigured 
	 */
	private function setClassName(Application $application, $pagePath) {
		// get controller class folder
		$folder = $application->getControllersPath();
	
		// gets page url
		$url = $pagePath;
	
		// get controller class name
		$file = "";
		$class = "";
		if(!$application->getAutoRouting()) {
			$path = $application->routes()->get($url)->getController();
			if(!$path) return;
			$file = ($folder?$folder."/":"").$path.".php";
			$slashPosition = strrpos($path,"/");
			if($slashPosition!==false) {
				$class = substr($path,$slashPosition+1);
				if(!$class) throw new XMLException("Invalid controller set for route: ".$url);
			} else {
				$class = $path;
			}
		} else {
			$class = str_replace(" ","",ucwords(str_replace(array("/","-")," ",strtolower($url))))."Controller";
			$file = $folder."/".$class.".php";
		}
		
		// loads controller file
		if(!file_exists($file)) throw new ServletException("Controller not found: ".$class);
		require_once($file);

		// validates and sets controller class
		if(!class_exists($class)) throw new ServletException("Controller class not found: ".$class);
		if(!is_subclass_of($class, __NAMESPACE__."\\"."Controller")) throw new ServletException($class." must be a subclass of Controller");
		$this->className = $class;
	}

	/**
	 * Gets controller class name.
	 *
	 * @return string
	 */
	public function getClassName() {
		return $this->className;
	}
}
