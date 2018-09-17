<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Locates view resolver based on response format name of page requested.
 */
final class ViewResolverLocator {	
	private $className;
	
	/**
	 * @param Application $application
	 * @param string $contentType
	 */
	public function __construct(Application $application, $contentType) {
		$this->setClassName($application, $contentType);
	}

	/**
	 * Gets resolver class name.
	 *
	 * @param Application $application
	 * @param string $contentType
	 * @throws ServletException
	 */
	private function setClassName(Application $application, $contentType) {
		// get listener path
		$resolverClass = "";
		$resolverLocation = "";
		
		// detect resolver @ application
		if($application->getViewResolversPath()) {
			$formats = $application->formats()->toArray();			
			foreach($formats as $format) {
				$resolverClass = $format->getViewResolver();
				if(strpos($contentType, $format->getContentType()) === 0 && $resolverClass) {
					$resolverLocation = $application->getViewResolversPath()."/".$resolverClass.".php";
					if(!file_exists($resolverLocation)) throw new ServletException("View resolver not found: ".$resolverLocation);
					require_once($resolverLocation);
					break;
				}
			}
		}
		
		// if no resolver was defined, do nothing
		if(!$resolverLocation) return;
		
		// validate resolver found or use default
		if(!class_exists($resolverClass)) throw new ServletException("View resolver class not defined: ".$resolverClass);
		
		$this->className = $resolverClass;
		
		// checks if it is a subclass of Controller
		if(!is_subclass_of($this->className, __NAMESPACE__."\\"."ViewResolver")) throw new ServletException($this->className." must be a subclass of ViewResolver");
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