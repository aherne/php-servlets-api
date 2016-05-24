<?php
/**
 * Attributes factory enveloping operations with COOKIE.
 */
final class Cookie extends AttributesFactory {
	/**
	 * Time by which current cookie expires. Can be changed at any point.
	 * 
	 * @var integer $intExpirationTime
	 */
	public static $intExpirationTime = 3600;
	
	/**
	 * Builds attributes from $_COOKIE
	 */
	public function __construct() {
		$this->tblAttributes = $_COOKIE;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Attributes::setAttribute()
	 */
	public function setAttribute($strKey, $mixValue) {
		parent::setAttribute($strKey, $mixValue);
		setcookie($strKey, $mixValue, time()+self::$intExpirationTime);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Attributes::removeAttribute()
	 */
	public function removeAttribute($strKey) {
		parent::removeAttribute($strKey);
		setcookie($strKey, "", time()-self::$intExpirationTime);
	}
}