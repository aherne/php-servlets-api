<?php
/**
 * Encapsulates session security settings on top of php.ini.
 */
final class CookieSecurityOptions {
	private $intExpiredTime = 0;
	private $blnIsHTTPSOnly = false;
	private $blnIsHTTPHeadersOnly = false;
	private $strPath = "";
	private $strDomain = "";
		
	/**
	 * Sets session cookie's expiration time.
	 * 
	 * @param integer $intSeconds
	 */
	public function setExpiredTime($intSeconds) {
		$this->intExpiredTime = time() + $intSeconds;
	}
	
	/**
	 * Gets cookie's expiration time.
	 * 
	 * @return integer
	 */
	public function getExpiredTime() {
		return $this->intExpiredTime;
	}
	
	/**
	 * Toggles restricting sessions to HTTPS only. If ON: HTTP cookies will not be accepted by server.
	 * 
	 * @param boolean $blnValue
	 */
	public function setSecuredByHTTPS($blnValue=false) {
		$this->blnIsHTTPS = (boolean) $blnValue;
	}
	
	/**
	 * Gets whether or not cookie is available only through HTTPs.
	 * 
	 * @return boolean
	 */
	public function isSecuredByHTTPS() {
		return $this->blnIsHTTPS;
	}
	
	/**
	 * Toggles restricting cookies to HTTP headers only. If ON: cookies not sent via HTTP headers will be ignored by server.
	 * @param boolean $blnValue
	 */
	public function setSecuredByHTTPheaders($blnValue=false) {
		$this->blnIsHTTPHeadersOnly = (boolean) $blnValue;
	}
	
	/**
	 * Gets whether or not cookie is available through HTTP only.
	 * 
	 * @return boolean
	 */
	public function isSecuredByHTTPheaders() {
		return $this->blnIsHTTPHeadersOnly;
	}
	
	/**
	 * Sets the path on the server in which the cookie will be available on.
	 * 
	 * @param string $strPath
	 */
	public function setPath($strPath = "") {
		$this->strPath = $strPath;
	}
	
	/**
	 * Gets the path on the server in which the cookie will be available on.
	 * 
	 * @return string
	 */
	public function getPath() {
		return $this->strPath;
	}
	
	/**
	 * Sets the (sub)domain that the cookie is available to.
	 * 
	 * @param string $strDomain
	 */
	public function setDomain($strDomain = "") {
		$this->strDomain = $strDomain;
	}

	/**
	 * Gets the (sub)domain that the cookie is available to.
	 *
	 * @return string
	 */
	public function getDomain() {
		return $this->strDomain;
	}
}