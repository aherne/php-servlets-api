<?php
require_once("AttributesFactory.php");
require_once("Runnable.php");
require_once("Controller.php");
require_once("Application.php");
require_once("Request.php");
require_once("Response.php");
require_once("Response/Wrapper.php");
require_once("Locators/ListenerLocator.php");
require_once("Locators/ControllerLocator.php");
require_once("Locators/WrapperLocator.php");
require_once("Listeners/ApplicationListener.php");
require_once("Listeners/RequestListener.php");
require_once("Listeners/ResponseListener.php");
require_once("Listeners/Implemented/PageValidatorListener.php");
require_once("Response/Implemented/ViewWrapper.php");

/**
 * Implements front controller MVC functionality. This is ServletsAPI focal point, integrating all components as a whole.
 */
final class FrontController {	
	const DEFAULT_DOCUMENT_DESCRIPTOR = "configuration.xml";
	
	/**
	 * Performs all steps described in class documentation
	 * 
	 * @throws ServletException
	 */
	public function __construct() {
		// get configuration object
		$objApplication = new Application(defined("DOCUMENT_DESCRIPTOR")?DOCUMENT_DESCRIPTOR:self::DEFAULT_DOCUMENT_DESCRIPTOR);
		
		// instances Listener locator
		$objListenerLocator = new ListenerLocator($objApplication);
		
		// operates custom changes on configuration object
		$tblListeners = $objListenerLocator->getClassNames("ApplicationListener");
		foreach($tblListeners as $strClassName) {
			$objRunnable = new $strClassName($objApplication);
			$objRunnable->run();
		}
		
		// sets request object
		$objRequest = new Request();
		
		// operates custom changes of request object. Appends PageValidatorListener!
		$tblListeners = array_merge(array("PageValidatorListener"), $objListenerLocator->getClassNames("RequestListener"));
		foreach($tblListeners as $strClassName) {
			$objRunnable = new $strClassName($objApplication, $objRequest);
			$objRunnable->run();
		}
		
		// sets response object
		$objResponse = new Response();
		$objResponse->setContentType($objRequest->getAttribute("page_content_type"));
		
		// locates and runs page controller
		$objControllerLocator = new ControllerLocator($objApplication, $objRequest->getAttribute("page_url"));
		$strClassName  = $objControllerLocator->getClassName();
		$objRunnable = new $strClassName($objApplication, $objRequest, $objResponse);
		$objRunnable->run();
		
		// locates a wrapper for view type and builds response
		$objWrapperLocator = new WrapperLocator($objApplication, $objResponse->getContentType());
		$objResponse->build($objWrapperLocator->getClassName());
		
		// operates custom changes on response object.
		$tblListeners = $objListenerLocator->getClassNames("ResponseListener");
		foreach($tblListeners as $strClassName) {
			$objRunnable = new $strClassName($objApplication, $objRequest, $objResponse);
			$objRunnable->run();
		}
							
		// commits response
		$objResponse->commit();		
	}
}