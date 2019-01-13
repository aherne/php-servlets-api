<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Encapsulates information about server that received the request.
 */
class RequestServer {
	private $name;
	private $iP;
	private $port;
	private $email;
	private $software;
	
	/**
	 * Detects info based on values in $_SERVER superglobal
	 */
	public function __construct() {
		$this->setIP();
		$this->setName();
		$this->setPort();
		$this->setEmail();
		$this->setSoftware();
	}
	
	/**
	 * Sets server host name.
	 */
	private function setName() {
		$this->name = $_SERVER["SERVER_NAME"];
	}
	
	/**
	 * Gets server host name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Sets server IP address.
	 */
	private function setIP() {
		$this->iP = $_SERVER["SERVER_ADDR"];
	
	}
	
	/**
	 * Gets server IP address
	 *
	 * @return string
	 */
	public function getIP() {
		return $this->iP;
	}
	
	
	/**
	 * Sets server port
	 */
	private function setPort() {
		$this->port = $_SERVER["SERVER_PORT"];
	}
	
	/**
	 * Gets server port
	 *
	 * @return int
	 */
	public function getPort() {
		return $this->port;
	}
	
	/**
	 * Sets server admin email.
	 */
	private function setEmail() {
		$this->email = $_SERVER["SERVER_ADMIN"];
	}
	
	/**
	 * Gets server admin email.
	 * 
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}
	
	/**
	 * Sets software web server is using.
	 */
	private function setSoftware() {
		$this->software = $_SERVER["SERVER_SOFTWARE"];
	}
	
	/**
	 * Gets software web server is using.
	 * 
	 * @return string
	 */
	public function getSoftware() {
		return $this->software;
	}
}