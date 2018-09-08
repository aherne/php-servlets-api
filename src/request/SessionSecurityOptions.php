<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Encapsulates session security settings on top of php.ini.
 */
final class SessionSecurityOptions {
	/**
	 * Set path that is going to be used when storing sessions.
	 * 
	 * @param string $path
	 */
	public function setSavePath($path) {
		ini_set("session.save_path",$path);
	}
	
	/**
	 * Sets name of session cookie.
	 * 
	 * @param string $name
	 */
	public function setName($name) {
		ini_set("session.name",$name);
	}
	
	/**
	 * Sets session cookie's expiration time.
	 * 
	 * @param integer $seconds
	 */
	public function setExpiredTime($seconds) {
		ini_set("session.gc_maxlifetime",$seconds);
	}
	
	/**
	 * Toggles session expiration on browser close.
	 * 
	 * @param boolean $value
	 */
	public function setExpiredOnBrowserClose($value=false) {
		ini_set("session.cookie_lifetime", ($value?1:0));
	}
	
	/**
	 * Toggles restricting sessions to HTTPS only. If ON: HTTP cookies will not be accepted by server.
	 * 
	 * @param boolean $value
	 */
	public function setSecuredByHTTPS($value=false) {
		ini_set("session.cookie_secure", ($value?1:0));
	}
	
	/**
	 * Toggles restricting sessions to HTTP headers only. If ON: cookies not sent via HTTP headers will be ignored by server.
	 * @param boolean $value
	 */
	public function setSecuredByHTTPheaders($value=false) {
		ini_set("session.cookie_httponly", ($value?1:0));
	}
		
	/**
	 * Toggles restricting sessions to those coming with a HTTP referrer LIKE %keyword%.
	 * 
	 * @param string $keyword
	 */
	public function setSecuredByReferrerCheck($keyword) {
		ini_set("session.referer_check", $keyword);
	}
}