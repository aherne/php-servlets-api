<?php
require_once("response/ResponseHeaders.php");
require_once("response/ResponseStream.php");
require_once("response/ResponseStatuses.php");

/**
 * Compiles information about response
 */
final class Response extends AttributesFactory {
	private $headers;
	private $HTTPStatusCode;
	private $viewPath;
	private $outputStream;
	private $isDisabled;
	
	public function __construct($contentType) {
		$this->outputStream	= new ResponseStream();
		
		$this->headers = new ResponseHeaders();
		$this->headers->set("Content-Type", $contentType);
	}
	
	/**
	 * Gets response stream to work on.
	 * 
	 * @return ResponseStream
	 */
	public function getOutputStream() {
		return $this->outputStream;
	}
	
	/**
	 * Delegates to specialized object for response header operations.
	 * 
	 * @return ResponseHeaders
	 */
	public function headers() {
		return $this->headers;
	}
	
	/**
	 * Redirects to a new location.
	 *
	 * @param string $location
	 * @param boolean $permanent
	 * @param boolean $preventCaching
	 * @return void
	 */
	public static function sendRedirect($location, $permanent=true, $preventCaching=false) {
		if($preventCaching) {
			header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
			header("Pragma: no-cache");
			header("Expires: 0");
		}
		header('Location: '.$location, true, $permanent?301:302);
		exit();
	}
	
	/**
	 * Forwards response to a file (aka "view")
	 * 
	 * @param string $viewPath
	 */
	public function setView($viewPath) {
		$this->viewPath = $viewPath;
	}
	
	/**
	 * Gets view's absolute path.
	 * 
	 * @return string
	 */
	public function getView() {
		return $this->viewPath;
	}
	
	/**
	 * Sets a HTTP status code in response.
	 * 
	 * @param int $HTTPStatusCode 
	 */
	public function setStatus($HTTPStatusCode) {
		$this->HTTPStatusCode = new ResponseStatuses($HTTPStatusCode);
	}
	
	/**
	 * Gets HTTP status code.
	 * 
	 * @return int 
	 */
	public function getStatus() {
		return $this->HTTPStatusCode;
	}
	
	/**
	 * Disables response. A disabled response will output nothing.
	 */
	public function disable() {
		$this->isDisabled = true;
	}
	
	/**
	 * Checks if response is disabled.
	 * 
	 * @return boolean
	 */
	public function isDisabled() {
	    return $this->isDisabled;
	}
	
	/**
	 * Commits response to client.
	 */
	public function commit() {
		if(!$this->isDisabled) {
			if(!headers_sent()) { // PHPUnit fix
                if($this->HTTPStatusCode) {
                    header("HTTP/1.1 ".$this->HTTPStatusCode->getStatus());
                }
				$headers = $this->headers->toArray();
				if(sizeof($headers)>0) {
					foreach($headers as $name=>$value) {
						header($name.": ".$value);
					}
				}
			}
			
			// show output
			echo $this->outputStream->get();
		} else {
            if($this->HTTPStatusCode) {
                header("HTTP/1.1 ".$this->HTTPStatusCode->getStatus());
            }
        }
	}
}
