<?php
/**
 * Encapsulates information about client that made the request.
 */
final class RequestClient {
	private $strIP;
	private $intPort;
	
	public function __construct() {
		$this->setIP();
		$this->setPort();
	}

	/**
	 * Gets  host name.
	 *
	 * @return string
	 */
	public function getName() {
		return @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
	}
	
	/**
	 * Sets  IP address.
	 */
	private function setIP() {
		$this->strIP = $_SERVER["REMOTE_ADDR"];
	
	}
	
	/**
	 * Gets  IP address
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