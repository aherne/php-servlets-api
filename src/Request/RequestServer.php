<?php
/**
 * Encapsulates information about server that received the request.
 */
final class RequestServer {
	private $strName;
	private $strIP;
	private $intPort;
	
	public function __construct() {
		$this->setIP();
		$this->setName();
		$this->setPort();
	}
	
	/**
	 * Sets server host name.
	 */
	private function setName() {
		$this->strName = $_SERVER["SERVER_NAME"];
	}
	
	/**
	 * Gets server host name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->strName;
	}
	
	/**
	 * Sets server IP address.
	 */
	private function setIP() {
		$this->strIP = $_SERVER["SERVER_ADDR"];
	
	}
	
	/**
	 * Gets server IP address
	 *
	 * @return string
	 */
	public function getIP() {
		return $this->strIP;
	}
	
	
	/**
	 * Sets server port
	 */
	private function setPort() {
		$this->intPort = $_SERVER["SERVER_PORT"];
	}
	
	/**
	 * Gets server port
	 *
	 * @return int
	 */
	public function getPort() {
		return $this->intPort;
	}
}