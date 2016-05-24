<?php
/**
 * Attributes factory enveloping operations with COOKIE.
 */
final class Cookie {
	/**
	 * Adds/updates a cookie param.
	 * 
	 * @param string $strKey
	 * @param mixed $mixValue
	 * @param integer $intExpiration Number of seconds until cookie expires.
	 */
	public function set($strKey, $mixValue, $intExpiration) {
		setcookie($strKey, $mixValue, time()+$intExpiration);
	}
	
	/**
	 * Gets value of cookie param.
	 * 
	 * @param string $strKey
	 * @return mixed
	 * @throws ServletException
	 */
	public function get($strKey) {
		if(!isset($_COOKIE[$strKey])) throw new ServletException("Cookie parameter not found!");
		return $_COOKIE[$strKey];
	}
	
	/**
	 * Checks if cookie param exists.
	 * 
	 * @param string $strKey
	 * @return boolean
	 */
	public function contains($strKey) {
		return isset($_COOKIE[$strKey]);
	}
	
	/**
	 * Deletes cookie param.
	 * 
	 * @param string $strKey
	 */
	public function remove($strKey) {
		setcookie($strKey, "", time()-self::DEFAULT_EXPIRATION_TIME);
	}
}