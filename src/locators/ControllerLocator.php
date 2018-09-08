<?php
namespace Lucinda\MVC\STDOUT;
/**
 * Locates controller based on page requested by client and values set in DD.
 */
final class ControllerLocator {
	private $className;
	
	/**
	 * @param Application $application
	 * @param string $pagePath
	 */
	public function __construct(Application $application, $pagePath) {
		$this->setClassName($application, $pagePath);
	}
	

	/**
	 * Sets controller class name. 
	 * 
	 * @param Application $application
	 * @param string $pagePath
	 * @throws ServletException
	 */
	private function setClassName(Application $application, $pagePath) {
		// get controller class folder
		$folder = $application->getControllersPath();
	
		// gets page url
		$uRL = $pagePath;
	
		// get controller class name
		$file = "";
		$class = "";
		if(!$application->getAutoRouting()) {
			$path = $application->getRouteInfo($uRL)->getController();
			if(!$path) return;
			$file = ($folder?$folder."/":"").$path.".php";
			$slashPosition = strrpos($path,"/");
			if($slashPosition!==false) {
				$class = substr($path,$slashPosition+1);
				if(!$class) throw new ServletException("Invalid controller set for route: ".$uRL);
			} else {
				$class = $path;
			}
		} else {
			$class = str_replace(" ","",ucwords(str_replace(array("/","-")," ",strtolower($uRL))))."Controller";
			$file = $folder."/".$class.".php";
		}
		
		// loads controller file
		if(!file_exists($file)) throw new ServletException("Controller not found: ".$class);
		require_once($file);

		// validates and sets controller class
		if(!class_exists($class)) throw new ServletException("Controller class not found: ".$class);
		if(!is_subclass_of($class, "Controller")) throw new ServletException($class." must be a subclass of Controller");
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
