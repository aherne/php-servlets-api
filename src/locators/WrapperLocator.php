<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Locates wrapper based on extension of page requested by client (or overridden by controller) and values set in DD.
 */
final class WrapperLocator {	
	const DEFAULT_WRAPPER = "ViewWrapper";
	private $className;
	
	/**
	 * @param Application $application
	 * @param string $contentType
	 */
	public function __construct(Application $application, $contentType) {
		$this->setClassName($application, $contentType);
	}

	/**
	 * Gets wrapper class name.
	 *
	 * @param Application $application
	 * @param string $contentType
	 * @throws ServletException
	 */
	private function setClassName(Application $application, $contentType) {
		// get listener path
		$wrapperClass = "";
		$wrapperLocation = "";
		
		// detect wrapper @ application
		if($application->getWrappersPath()) {
			$formats = $application->getFormats();			
			foreach($formats as $format) {
				$wrapperClass = $format->getWrapper();
				if(strpos($contentType, $format->getContentType()) === 0 && $wrapperClass) {
					$wrapperLocation = $application->getWrappersPath()."/".$wrapperClass.".php";
					if(!file_exists($wrapperLocation)) throw new ServletException("Wrapper not found: ".$wrapperLocation);
					require_once($wrapperLocation);
					break;
				}
			}
		}
		
		// if no wrapper was defined, use ViewWrapper
		if(!$wrapperLocation) $wrapperClass = self::DEFAULT_WRAPPER;
		
		// validate wrapper found or use default
		if(!class_exists($wrapperClass)) throw new ServletException("Wrapper class not defined: ".$wrapperClass);
		
		$this->className = $wrapperClass;
		
		// checks if it is a subclass of Controller
		if(!is_subclass_of($this->className, "Wrapper")) throw new ServletException($this->className." must be a subclass of Wrapper");
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