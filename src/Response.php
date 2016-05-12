<?php
require_once("Response/ResponseStream.php");
require_once("Response/ResponseStatuses.php");

/**
 * Compiles information about response
 */
final class Response extends AttributesFactory {
	private $tblHeaders = array();
	private $intHTTPStatusCode = ResponseStatuses::SC_OK;
	private $strContentType;
	private $strCharacterEncoding;
	private $strViewPath;
	private $objOutputStream;
	private $blnIsDisabled;
	
	public function __construct() {
		$this->objOutputStream	= new ResponseStream();
	}
	
	/**
	 * Changes response content type.
	 * 
	 * @param string $strContentType
	 * @throws ServletException
	 * @return void
	 */
	public function setContentType($strContentType) {
		$this->strContentType = $strContentType;
	}
	
	/**
	 * Gets response content type.
	 * 
	 * @return string
	 */
	public function getContentType() {
		return $this->strContentType;
	}
	
	/**
	 * Sets response character encoding.
	 * 
	 * @param string $strCharacterEncoding
	 * @return void
	 */
	public function setCharacterEncoding($strCharacterEncoding) {
		$this->strCharacterEncoding = $strCharacterEncoding;
	}
	
	/**
	 * Gets response character encoding.
	 * @return string
	 */
	public function getCharacterEncoding() {
		return $this->strCharacterEncoding;
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
	 * Sets a response header.
	 * 
	 * @param string $strName
	 * @param string $strValue
	 * @return void
	 */
	public function setHeader($strName, $strValue) {
		$this->tblHeaders[$strName] = $strValue;
	}
	
	/**
	 * Gets a response header
	 * 
	 * @param string $strName
	 * @return string
	 */
	public function getHeader($strName) {
		return $this->tblHeaders[$strName];
	}
	
	/**
	 * Exits to a new location.
	 * 
	 * @param string $strLocation
	 * @return void
	 */
	public static function sendRedirect($strLocation) {
		header('Location: '.$strLocation);
		exit();
	}
	
	/**
	 * Sets view's absolute path.
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
	 * Builds a response into output buffer and writes it to output stream.
	 * 
	 * @param string $strWrapperClass
	 * @throws ServletException
	 */
	public function build($strWrapperClass) {
		// If stream has already been written to OR response was disabled, do not build
		if($this->objOutputStream->get() || $this->blnIsDisabled) {
			return; 
		}
		
		// find and run wrapper class
		ob_start();
		$objRunnable = new $strWrapperClass($this);
		$objRunnable->run();
		$strContents = ob_get_contents();
		ob_end_clean();
		
		// write string to output stream
		$this->objOutputStream->write($strContents);
	}
	
	/**
	 * Commits response to client.
	 */
	public function commit() {	
		if($this->blnIsDisabled) {
			http_response_code($this->intHTTPStatusCode);
		} else {
			// set content type
			header("Content-Type: ".$this->getContentType().($this->getCharacterEncoding()?"; charset=".$this->getCharacterEncoding():""));
			
			// set objHeaders
			if(sizeof($this->tblHeaders)>0) {
				foreach($this->tblHeaders as $strName=>$strValue) {
					header($strName.": ".$strValue);
				}
			}
			
			// show output
			echo $this->objOutputStream->get();
		}
	}
}