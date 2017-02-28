<?php
/**
 * Abstract class all servlet components extend.
 */
class AttributesFactory {
	/**
	 * Property that must be set by children of class.
	 * 
	 * @var array
	 */
	protected $tblAttributes =  array();
	
	/**
	 * Gets attribute by name.
	 * 
	 * @param string $strKey
	 * @return mixed
	 */
	public function getAttribute($strKey) {
		return (isset($this->tblAttributes[$strKey])?$this->tblAttributes[$strKey]:null);
	}	
	
	/**
	 * Sets attribute by name & value.
	 * 
	 * @param string $strKey
	 * @param mixed $mixValue
	 */
	public function setAttribute($strKey, $mixValue) {
		$this->tblAttributes[$strKey] = $mixValue;
	}
	
	/**
	 * Deletes attribute by name.
	 * 
	 * @param string $strKey
	 * @return void
	 */
	public function removeAttribute($strKey) {
		unset($this->tblAttributes[$strKey]);
	}
	
	/**
	 * Checks if attribute exists.
	 * 
	 * @param string $strKey
	 * @return boolean
	 */
	public function isAttribute($strKey) {
		return isset($this->tblAttributes[$strKey]);
	}
	
	/**
	 * Decapsulates attributes as array.
	 * 
	 * @return array
	 */
	public function toArray() {
		return $this->tblAttributes;
	}
	
	/**
	 * Checks if component has no attributes.
	 * 
	 * @return boolean
	 */
	public function isEmpty() {
		return empty($this->tblAttributes);
	}
}