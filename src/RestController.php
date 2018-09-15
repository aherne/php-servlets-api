<?php
namespace Lucinda\MVC\STDOUT;

require_once("exceptions/MethodNotAllowedException.php");

/**
 * Defines an abstract RESTful controller. Classes extending it must have methods whose name is identical to request methods they are expecting.
 * 
 * Example:
 * // listens to http://lucian.com/example (PUT):
 * class ExampleController extends RestController {
 * 		protected function put() {
 * 			// will be triggered whenever someone makes a PUT request to path listened (routed) by ExampleController
 * 		}
 * }
 */
abstract class RestController extends Controller {
	public function run() {
	    $methodName = strtolower($this->request->getMethod());
	    if(method_exists($this, $methodName)) {
	        $this->$methodName();
	    } else {
	        throw new MethodNotAllowedException();
	    }
	}
}