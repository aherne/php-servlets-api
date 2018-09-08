<?php
namespace Lucinda\MVC\STDOUT;

require_once("Runnable.php");
require_once("Controller.php");
require_once("Application.php");
require_once("Request.php");
require_once("Response.php");
require_once("response/Wrapper.php");
require_once("locators/ListenerLocator.php");
require_once("locators/ControllerLocator.php");
require_once("locators/WrapperLocator.php");
require_once("listeners/ApplicationListener.php");
require_once("listeners/RequestListener.php");
require_once("listeners/ResponseListener.php");
require_once("implemented/ViewWrapper.php");
require_once("implemented/PageValidator.php");

/**
 * Implements front controller MVC functionality. This is ServletsAPI focal point, integrating all components as a whole.
 */
final class FrontController {	
	/**
	 * Performs all steps described in class documentation
	 * 
	 * @throws ServletException
	 */
	public function __construct($documentDescriptor="configuration.xml") {
		// sets application object
		$application = new Application($documentDescriptor);
		
		// instances Listener locator
		$listenerLocator = new ListenerLocator($application);
		
		// operates custom changes on configuration object
		$listeners = $listenerLocator->getClassNames("ApplicationListener");
		foreach($listeners as $className) {
			$runnable = new $className($application);
			$runnable->run();
		}
		
		// sets request object
		$request = new Request();
		$request->setValidator(new PageValidator($request->getURI()->getPage(), $application));
		
		// operates custom changes on request object.
		$listeners = $listenerLocator->getClassNames("RequestListener");
		foreach($listeners as $className) {
			$runnable = new $className($application, $request);
			$runnable->run();
		}
		
		// sets response object
		$response = new Response($request->getValidator()->getContentType());
		if(!$application->getAutoRouting()) {
		    $view = $application->routes()->get($request->getValidator()->getPage())->getView();
		    if($view) {
		        $response->setView($view);
		    }
		}
		
		// locates and runs page controller
		$controllerLocator = new ControllerLocator($application, $request->getValidator()->getPage());
		$className  = $controllerLocator->getClassName();
		if($className) {
			$runnable = new $className($application, $request, $response);
			$runnable->run();
		}
		
		// if response is not disabled, produce a view
		if(!$response->isDisabled()) {
			// locates a wrapper for view type and builds response
			if($response->getOutputStream()->isEmpty()) {
				// locates and instances wrapper
				$wrapperLocator = new WrapperLocator($application, $response->headers()->get("Content-Type"));
				$className  = $wrapperLocator->getClassName();
				if($className == WrapperLocator::DEFAULT_WRAPPER && $application->getViewsPath()) {
					$response->setView($application->getViewsPath()."/".$response->getView());
				}
				$runnable = new $className($response);
	    		
				// builds response
				ob_start();
				$runnable->run();
				$contents = ob_get_contents();
				ob_end_clean();
			    
				// writes response to output stream
				$response->getOutputStream()->write($contents);
			}
		
			// operates custom changes on response object
			$listeners = $listenerLocator->getClassNames("ResponseListener");
			foreach($listeners as $className) {
				$runnable = new $className($application, $request, $response);
				$runnable->run();
			}
		}
							
		// commits response
		$response->commit();		
	}
}
