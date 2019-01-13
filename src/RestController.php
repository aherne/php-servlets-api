<?php
namespace Lucinda\MVC\STDOUT;

require_once("exceptions/MethodNotAllowedException.php");

/**
 * Defines an abstract RESTful controller. Classes extending it must have methods whose name is identical to request methods they are expecting.
 */
abstract class RestController extends Controller {
    /**
     * {@inheritDoc}
     * @see Runnable::run()
     */
	public function run() {
	    $methodName = strtolower($this->request->getMethod());
	    if(method_exists($this, $methodName)) {
	        $this->$methodName();
	    } else {
	        throw new MethodNotAllowedException();
	    }
	}
}