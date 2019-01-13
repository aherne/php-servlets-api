<?php
namespace Lucinda\MVC\STDOUT;

require_once("CookieSecurityOptions.php");

/**
 * Attributes factory enveloping operations with COOKIE.
 */
class Cookie implements MutableAttributes {
	/**
	 * Adds/updates a cookie param.
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @param CookieSecurityOptions $securityOptions
	 */
	public function set($key, $value, CookieSecurityOptions $securityOptions=null) {
		$answer = false;
		if($securityOptions) {
			$answer = setcookie($key, $value, $securityOptions->getExpiredTime(), $securityOptions->getPath(), $securityOptions->getDomain(), $securityOptions->isSecuredByHTTPS(), $securityOptions->isSecuredByHTTPheaders());
		} else {
			$answer = setcookie($key, $value);
		}
		if(!$answer) throw new ServletException("Cookie could not be set!");
	}
	
	/**
	 * Gets value of cookie param.
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		return $_COOKIE[$key];
	}
	
	/**
	 * Checks if cookie param exists.
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function contains($key) {
		return isset($_COOKIE[$key]);
	}
	
	/**
	 * Deletes cookie param.
	 * 
	 * @param string $key
	 */
	public function remove($key) {
		setcookie ($key, "", 1);
		setcookie ($key, false);
		unset($_COOKIE[$key]);
	}
}