<?php
require_once("response/ResponseHeaders.php");
require_once("response/ResponseStream.php");
require_once("response/ResponseStatuses.php");

/**
 * Compiles information about response
 */
final class Response extends AttributesFactory {
	private $objHeaders;
	private $intHTTPStatusCode = ResponseStatuses::SC_OK;
	private $strViewPath;
	private $objOutputStream;
	private $blnIsDisabled;
	
	public function __construct($strContentType) {
		$this->objOutputStream	= new ResponseStream();
		
		$this->objHeaders = new ResponseHeaders();
		$this->objHeaders->set("Content-Type", $strContentType);
	}
	
	/**
	 * Gets response stream to work on.
	 * 
	 * @return ResponseStream
	 */
	public function getOutputStream() {
		return $this->objOutputStream;
	}
	
	/**
	 * Delegates to specialized object for response header operations.
	 * 
	 * @return ResponseHeaders
	 */
	public function headers() {
		return $this->objHeaders;
	}
	
	/**
	 * Redirects to a new location.
	 *
	 * @param string $strLocation
	 * @param boolean $blnPermanent
	 * @param boolean $preventCaching
	 * @return void
	 */
	public static function sendRedirect($strLocation, $blnPermanent=true, $preventCaching=false) {
		if($preventCaching) {
			header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
			header("Pragma: no-cache");
			header("Expires: 0");
		}
		header('Location: '.$strLocation, true, $blnPermanent?301:302);
		exit();
	}
	
	/**
	 * Forwards response to a file (aka "view")
	 * 
	 * @param string $strViewPath
	 */
	public function setView($strViewPath) {
		$this->strViewPath = $strViewPath;
	}
	
	/**
	 * Gets view's absolute path.
	 * 
	 * @return string
	 */
	public function getView() {
		return $this->strViewPath;
	}
	
	/**
	 * Sets a HTTP status code in response.
	 * 
	 * @param int $intHTTPStatusCode 
	 */
	public function setStatus($intHTTPStatusCode) {
		$this->intHTTPStatusCode = $intHTTPStatusCode;
	}
	
	/**
	 * Gets HTTP status code.
	 * 
	 * @return int 
	 */
	public function getStatus() {
		return $this->intHTTPStatusCode;
	}
	
	/**
	 * Disables response. A disabled response will output nothing.
	 */
	public function disable() {
		$this->blnIsDisabled = true;
	}
	
	/**
	 * Checks if response is disabled.
	 * 
	 * @return boolean
	 */
	public function isDisabled() {
	    return $this->blnIsDisabled;
	}
	
	/**
	 * Commits response to client.
	 */
	public function commit() {	
		if($this->blnIsDisabled) {
			http_response_code($this->intHTTPStatusCode);
		} else {
			if(!headers_sent()) { // PHPUnit fix
				$headers = $this->objHeaders->toArray();
				if(sizeof($headers)>0) {
					foreach($headers as $strName=>$strValue) {
						header($strName.": ".$strValue);
					}
				}
			}
			
			// show output
			echo $this->objOutputStream->get();
		}
	}
}
