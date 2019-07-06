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
	    $methodName = strtoupper($this->request->getMethod());
	    if(method_exists($this, $methodName)) {
	        $this->$methodName();
	    } else {
	        throw new MethodNotAllowedException();
	    }
	}
	
	/**
	 * Support HTTP OPTIONS requests by default
	 */
	public function OPTIONS() {
	    $options = array();
	    $validHTTPMethods = array("GET","POST","PUT","DELETE","HEAD","OPTIONS","CONNECT","TRACE");
	    foreach($validHTTPMethods as $methodName) {
	        if(method_exists($this, $methodName)) {
	           $options[] = $methodName;
	        }
	    }
	    $this->response->headers("Allow", implode(", ", $options));
	}
}