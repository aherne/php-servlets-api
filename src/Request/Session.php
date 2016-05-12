<?php
/**
 * Attributes factory enveloping operations with SESSION.
 */
final class Session extends AttributesFactory {
	/**
	 * Starts session and builds attributes from $_SESSIOn
	 */
	public function __construct() {
		// start session
		if (session_id() == "") session_start();
		
		// populate
		$this->tblAttributes = $_SESSION;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Attributes::setAttribute()
	 */
	public function setAttribute($strKey, $mixValue) {
		parent::setAttribute($strKey, $mixValue);
		$_SESSION[$strKey] = $mixValue;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Attributes::removeAttribute()
	 */
	public function removeAttribute($strKey) {
		parent::removeAttribute($strKey);
		unset($_SESSION[$strKey]);
	}
}