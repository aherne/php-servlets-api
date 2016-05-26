<?php
/**
 * Defines an abstract RESTful controller. Classes extending it must implement methods that correspond to HTTP verbs they need.
 * 
 * Example:
 * // listens to http://lucian.com/example (PUT):
 * class ExampleController extends RestController {
 * 		public function PUT() {
 * 			// will be triggered whenever someone makes a PUT request to path listened (routed) by ExampleController
 * 		}
 * }
 */
abstract class RestController extends Controller {
	public function run() {
		$strMethod = strtoupper($this->request->getMethod());
		if(!method_exists($this, $strMethod)) {
			throw new ServletException("Method not implemented: ".$strMethod);
		}
		$this->$strMethod();
	}
}