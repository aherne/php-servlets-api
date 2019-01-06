<?php
namespace Lucinda\MVC\STDOUT;

require_once("attributes/MutableAttributesFactory.php");
require_once("attributes/ImmutableAttributesFactory.php");
require_once("request/RequestClient.php");
require_once("request/RequestServer.php");
require_once("request/RequestURI.php");
require_once("request/UploadedFileTree.php");
require_once("request/Session.php");
require_once("request/Cookie.php");
require_once("request/RequestValidator.php");

/**
 * Detects information about request from $_SERVER, $_GET, $_POST, $_FILES. Once detected, parameters are immutable.
 */
final class Request {
	private $client;
	private $server;
	private $uRI;
	private $method;
	private $protocol;
	
	private $cookie;
	private $session;
	private $headers;
	private $parameters;
	private $uploadedFiles;
	
	private $validator;
	private $attributes;
	
	/**
	 * Detects all aspects of a request.
	 */
	public function __construct() {
	    $this->attributes = new MutableAttributesFactory();
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
		$this->client = new RequestClient();
	}

	/**
	 * Gets information about client that made the request.
	 * @return RequestClient
	 */
	public function getClient() {
		return $this->client;
	}

	/**
	 * Sets information about server that received the request.
	 */
	private function setServer() {
		$this->server = new RequestServer();
	}

	/**
	 * Gets information about server that received the request.
	 * @return RequestServer
	 */
	public function getServer() {
		return $this->server;
	}

	/**
	 * Sets information about URI client requested (host, page, url, etc).
	 */
	private function setURI() {
		$this->uRI = new RequestURI();
	}

	/**
	 * Gets information about URI client requested (host, page, url, etc).
	 * @return RequestURI
	 */
	public function getURI() {
		return $this->uRI;
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
		$this->headers = new ImmutableAttributesFactory($headers);
	}

	/**
	 * Sets parameters sent by client in accordance to HTTP request method.
	 */
	private function setParameters() {
	    $parameters = array();
		switch($_SERVER["REQUEST_METHOD"]) {
			case "GET":
			    $parameters = $_GET;
				break;		
			case "POST":
			    $parameters = $_POST;
				break;		
			case "PUT":
			case "DELETE":
			    $post_vars = array();
				parse_str(file_get_contents("php://input"),$post_vars);
				$parameters = $post_vars;
				break;
			default:
			    $parameters = array();
				break;
		}		
		$this->parameters = new ImmutableAttributesFactory($parameters);
	}
	
	/**
	 * Sets files uploaded by client via form based on PHP superglobal $_FILES with two structural changes:
	 * - uploaded file attributes (name, type, tmp_name, etc) are encapsulated into an UploadedFile instance
	 * - array structure information is saved to follows exactly structure set in file input @ form. This means:
	 * 			<input... name="a[b][c]">
	 * 	 will once posted reflect into item:
	 * 			array("a"=>array("b"=>array("c"=>$uploadedFile)))
	 * 	 instead of:
	 * 			array("a"=>array("name"=>array("b"=>array("c"=>"myName")),...) 
	 */
	private function setUploadedFiles() {
		$files = new UploadedFileTree();
		$this->uploadedFiles = $files->toArray();
	}
	
	/**
	 * Gets files originally uploaded by client under this structure:
	 * - array structure follows name of form input. This means:
	 * 			<input type="file" name="a[b][c]">
	 * 	 will once posted be seen as:
	 * 			array("a"=>array("b"=>array("c"=>$uploadedFile)))
	 * 	 instead of $_FILES structure:
	 * 			array("a"=>array("name"=>array("b"=>array("c"=>"myName")),...) 
	 * - uploaded file attributes (name, type, tmp_name, etc) are encapsulated into UploadedFile instance
	 * 
	 * @return array
	 */
	public function getUploadedFiles() {
		return $this->uploadedFiles;
	}
	
	/**
	 * Encapsulates parameters received as $_COOKIE
	 */
	private function setCookie() {
		$this->cookie = new Cookie();
	}

	/**
	 * Gets pointer to cookie (to be used in gettin/setting cookie params)
	 *
	 * @return Cookie
	 */
	public function getCookie() {
		return $this->cookie;
	}

	/**
	 * Encapsulates parameters received as $_SESSION
	 */
	private function setSession() {
		$this->session = new Session();
	}


	/**
	 * Gets pointer to cookie (to be used in gettin/setting session params)
	 *
	 * @return Session
	 */
	public function getSession() {
		return $this->session;
	}

	/**
	 * Sets HTTP request method in which URI was requested.
	 *
	 * @return void
	 */
	private function setMethod() {
		$this->method=$_SERVER["REQUEST_METHOD"];
	}
	
	/**
	 * Gets HTTP request method in which URI was requested.
	 *
	 * @example GET
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}
	
	/**
	 * Sets protocol for which URI was requested.
	 */
	private function setProtocol() {
		$this->protocol = (!empty($_SERVER['HTTPS'])?"https":"http");
	}
	
	/**
	 * Gets protocol for which URI was requested.
	 *
	 * @example https
	 * @return string
	 */
	public function getProtocol() {
		return $this->protocol;
	}
	
	/**
	 * Gets input stream contents.
	 * 
	 * @return string
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
	
	/**
	 * Gets a pointer to factory that encapsulates user-defined attributes.
	 *
	 * @return \Lucinda\MVC\STDOUT\MutableAttributesFactory
	 */
	public function attributes() {
	    return $this->attributes;
	}
	
	/**
	 * Gets a pointer to factory that encapsulates headers received from client.
	 *
	 * @return \Lucinda\MVC\STDOUT\ImmutableAttributesFactory
	 */
	public function headers() {
	    return $this->headers;
	}
	
	/**
	 * Gets a pointer to factory that encapsulates parameters associated to request method (GET/POST/PUT/DELETE) received from client.
	 *
	 * @return \Lucinda\MVC\STDOUT\ImmutableAttributesFactory
	 */
	public function parameters() {
	    return $this->parameters;
	}
}
