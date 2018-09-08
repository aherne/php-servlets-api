<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Locates listener based on component name.
 */
final class ListenerLocator {
	private $classNames = array();
	
	public function __construct($application) {
		$this->setClassNames($application);
	}

	/**
	 * Locates listeners by component name (configuration | request | response).
	 *
	 * @throws ServletException
	 */
	private function setClassNames(Application $application) {
		$listenerPath = $application->getListenersPath();
		$listeners = $application->getListeners();
		
		// gets classes
		$output = array();
		foreach($listeners as $className) {
			// load file
			$file = $listenerPath."/".$className.".php";
			if(!file_exists($file)) throw new ServletException("Listener file not found: ".$file);
			require_once($file);
				
			// verify class
			if(!class_exists($className)) throw new ServletException("Listener class not found: ".$className);
			$output[] = $className;
		}
		
		$this->classNames = $output;
	}
	
	/**
	 * Gets class names by parent class name.
	 * 
	 * @param string $parentClassName
	 * @return string[]
	 */
	public function getClassNames($parentClassName) {
		$output = array();
		foreach($this->classNames as $className) {
			if(is_subclass_of($className, $parentClassName)) {
				$output[] = $className;
			}
		}
		return $output;
	}
}