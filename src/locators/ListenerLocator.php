<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Locates all event listeners based on data in XML tag <listeners>.
 */
class ListenerLocator {
	private $classNames = array();
	
	/**
	 * Detects all event listeners based on entries in XML tag <listeners>. 
	 * 
	 * @param Application $application
	 * @throws ServletException If listener file could not be located on disk.
	 */
	public function __construct(Application $application) {
		$this->setClassNames($application);
	}

	/**
	 * Locates listeners by component name (configuration | request | response).
	 *
	 * @param Application $application
	 * @throws ServletException If listener file could not be located on disk.
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
	 * Gets event listener class names by parent class name.
	 * 
	 * @param string $parentClassName
	 * @return string[]
	 */
	public function getClassNames($parentClassName) {
		$output = array();
		foreach($this->classNames as $className) {
		    if(is_subclass_of($className, __NAMESPACE__."\\".$parentClassName)) {
				$output[] = $className;
			}
		}
		return $output;
	}
}