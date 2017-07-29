<?php
require_once("request/RequestClient.php");
require_once("request/RequestServer.php");
require_once("request/RequestURI.php");
require_once("request/RequestParameters.php");
require_once("request/UploadedFileTree.php");
require_once("request/Session.php");
require_once("request/Cookie.php");
require_once("request/RequestValidator.php");

/**
 * Detects information about request from $_SERVER, $_GET, $_POST, $_FILES. Once detected, parameters are immutable.
 */
final class Request extends AttributesFactory {
	private $objClient;
	private $objServer;
	private $objURI;
	private $strMethod;
	private $strProtocol;
	
	private $objCookie;
	private $objSession;
	private $objHeaders;
	private $objPostParameters;
	private $tblUploadedFiles;
	
	private $validator;
	
	/**
	 * Detects all aspects of a request.
	 */
	public function __construct() {
		$this->setClient();
		$this->setServer();
		$this->setMethod();
		$this->setProtocol();
		$this->setURI();
		// set params
		$this->setCookie();
		$this->setSession();
		$this->setHeaders();
		$this->setParameters();
		$this->setUploadedFiles();
	}
	
	/**
	 * Sets information about client that made the request.
	 */
	private function setClient() {
		$this->objClient = new RequestClient();
	}

	/**
	 * Gets information about client that made the request.
	 * @return RequestClient
	 */
	public function getClient() {
		return $this->objClient;
	}

	/**
	 * Sets information about server that received the request.
	 */
	private function setServer() {
		$this->objServer = new RequestServer();
	}

	/**
	 * Gets information about server that received the request.
	 * @return RequestServer
	 */
	public function getServer() {
		return $this->objServer;
	}

	/**
	 * Sets information about URI client requested (host, page, url, etc).
	 */
	private function setURI() {
		$this->objURI = new RequestURI();
	}

	/**
	 * Gets information about URI client requested (host, page, url, etc).
	 */
	public function getURI() {
		return $this->objURI;
	}
	
	/**
	 * Sets headers sent by client.
	 */
	private function setHeaders() {
		$headers = array();
		foreach($_SERVER as $name => $value){
			if(strpos($name, "HTTP_") === 0){
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		} 
		$this->objHeaders = new RequestParameters($headers);
	}
	
	/**
	 * Gets headers originally sent by client.
	 * 
	 * @return RequestParameters
	 */
	public function getHeaders() {
		return $this->objHeaders;
	}

	/**
	 * Sets parameters posted by client, based on PHP superglobal $_POST.
	 */
	private function setParameters() {
		$this->objParameters = new RequestParameters($_POST);
	}
	
	/**
	 * Gets parameters received via a POST request.
	 * 
	 * @return RequestParameters
	 */
	public function getParameters() {
		return $this->objParameters;
	}
	
	/**
	 * Sets files uploaded by client via form based on PHP superglobal $_FILES with two structural changes:
	 * - uploaded file attributes (name, type, tmp_name, etc) are encapsulated into an UploadedFile instance
	 * - array structure information is saved to follows exactly structure set in file input @ form. This means:
	 * 			<input... name="a[b][c]">
	 * 	 will once posted reflect into item:
	 * 			array("a"=>array("b"=>array("c"=>$objUploadedFile)))
	 * 	 instead of:
	 * 			array("a"=>array("name"=>array("b"=>array("c"=>"myName")),...) 
	 */
	private function setUploadedFiles() {
		$objFiles = new UploadedFileTree();
		$this->tblUploadedFiles = $objFiles->toArray();
	}
	
	/**
	 * Gets files originally uploaded by client under this structure:
	 * - array structure follows name of form input. This means:
	 * 			<input type="file" name="a[b][c]">
	 * 	 will once posted be seen as:
	 * 			array("a"=>array("b"=>array("c"=>$objUploadedFile)))
	 * 	 instead of $_FILES structure:
	 * 			array("a"=>array("name"=>array("b"=>array("c"=>"myName")),...) 
	 * - uploaded file attributes (name, type, tmp_name, etc) are encapsulated into UploadedFile instance
	 * 
	 * @return array
	 */
	public function getUploadedFiles() {
		return $this->tblUploadedFiles;
	}
	
	/**
	 * Encapsulates parameters received as $_COOKIE
	 */
	private function setCookie() {
		$this->objCookie = new Cookie();
	}

	/**
	 * Gets pointer to cookie (to be used in gettin/setting cookie params)
	 */
	public function getCookie() {
		return $this->objCookie;
	}

	/**
	 * Encapsulates parameters received as $_SESSION
	 */
	private function setSession() {
		$this->objSession = new Session();
	}


	/**
	 * Gets pointer to cookie (to be used in gettin/setting session params)
	 */
	public function getSession() {
		return $this->objSession;
	}

	/**
	 * Sets HTTP request method in which URI was requested.
	 *
	 * @return void
	 */
	private function setMethod() {
		$this->strMethod=$_SERVER["REQUEST_METHOD"];
	}
	
	/**
	 * Gets HTTP request method in which URI was requested.
	 *
	 * @example GET
	 * @return string
	 */
	public function getMethod() {
		return $this->strMethod;
	}
	
	/**
	 * Sets protocol for which URI was requested.
	 */
	private function setProtocol() {
		$this->strProtocol = (!empty($_SERVER['HTTPS'])?"https":"http");
	}
	
	/**
	 * Gets protocol for which URI was requested.
	 *
	 * @example https
	 * @return string
	 */
	public function getProtocol() {
		return $this->strProtocol;
	}
	
	/**
	 * Gets input stream contents.
	 * 
	 * @return binary
	 */
	public function getInputStream() {
		return file_get_contents("php://input");
	} 
	
	/**
	 * Sets class able to process request and extract information about:
	 * - actual page requested
	 * - content type requested
	 * - path parameters present in request
	 * 
	 * @param RequestValidator $validator
	 */
	public function setValidator(RequestValidator $validator) {
		$this->validator = $validator;
	}
	
	/**
	 * Gets class to extract information about:
	 * - actual page requested
	 * - content type requested
	 * - path parameters present in request
	 * 
	 * @return RequestValidator
	 */
	public function getValidator() {
		return $this->validator;
	}
}
