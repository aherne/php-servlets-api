<?php
/**
 * Encapsulates information about client that made the request.
 */
final class RequestClient {
	private $strName;
	private $strIP;
	private $intPort;
	
	public function __construct() {
		$this->setName();
		$this->setIP();
		$this->setPort();
	}
	
	/**
	 * Sets host name.
	 */
	public function setName() {
		$this->strIP = (isset($_SERVER["REMOTE_HOST"])?$_SERVER["REMOTE_HOST"]:"");
	}

	/**
	 * Gets host name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->strName;
	}
	
	/**
	 * Sets IP address.
	 */
	private function setIP() {
		$this->strIP = $_SERVER["REMOTE_ADDR"];
	
	}
	
	/**
	 * Gets IP address
	 *
	 * @return string
	 */
	public function getIP() {
		return $this->strIP;
	}
	
	/**
	 * Sets  port
	 */
	private function setPort() {
		$this->intPort = $_SERVER["REMOTE_PORT"];
	}
	
	/**
	 * Gets  port
	 *
	 * @return int
	 */
	public function getPort() {
		return $this->intPort;
	}
}