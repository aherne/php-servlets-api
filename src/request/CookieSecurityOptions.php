<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Encapsulates session security settings on top of php.ini.
 */
class CookieSecurityOptions {
	private $expiredTime = 0;
	private $isHTTPSOnly = false;
	private $isHTTPHeadersOnly = false;
	private $path = "";
	private $domain = "";
		
	/**
	 * Sets session cookie's expiration time.
	 * 
	 * @param integer $seconds
	 */
	public function setExpiredTime($seconds) {
		$this->expiredTime = time() + $seconds;
	}
	
	/**
	 * Gets cookie's expiration time.
	 * 
	 * @return integer
	 */
	public function getExpiredTime() {
		return $this->expiredTime;
	}
	
	/**
	 * Toggles restricting sessions to HTTPS only. If ON: HTTP cookies will not be accepted by server.
	 * 
	 * @param boolean $value
	 */
	public function setSecuredByHTTPS($value=false) {
		$this->isHTTPS = (boolean) $value;
	}
	
	/**
	 * Gets whether or not cookie is available only through HTTPs.
	 * 
	 * @return boolean
	 */
	public function isSecuredByHTTPS() {
		return $this->isHTTPS;
	}
	
	/**
	 * Toggles restricting cookies to HTTP headers only. If ON: cookies not sent via HTTP headers will be ignored by server.
	 * @param boolean $value
	 */
	public function setSecuredByHTTPheaders($value=false) {
		$this->isHTTPHeadersOnly = (boolean) $value;
	}
	
	/**
	 * Gets whether or not cookie is available through HTTP only.
	 * 
	 * @return boolean
	 */
	public function isSecuredByHTTPheaders() {
		return $this->isHTTPHeadersOnly;
	}
	
	/**
	 * Sets the path on the server in which the cookie will be available on.
	 * 
	 * @param string $path
	 */
	public function setPath($path = "") {
		$this->path = $path;
	}
	
	/**
	 * Gets the path on the server in which the cookie will be available on.
	 * 
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}
	
	/**
	 * Sets the (sub)domain that the cookie is available to.
	 * 
	 * @param string $domain
	 */
	public function setDomain($domain = "") {
		$this->domain = $domain;
	}

	/**
	 * Gets the (sub)domain that the cookie is available to.
	 *
	 * @return string
	 */
	public function getDomain() {
		return $this->domain;
	}
}