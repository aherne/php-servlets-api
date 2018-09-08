<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Abstract class all servlet components extend.
 */
class AttributesFactory {
	/**
	 * Property that must be set by children of class.
	 * 
	 * @var array
	 */
	protected $attributes =  array();
	
	/**
	 * Gets attribute by name.
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function getAttribute($key) {
		return (isset($this->attributes[$key])?$this->attributes[$key]:null);
	}	
	
	/**
	 * Sets attribute by name & value.
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function setAttribute($key, $value) {
		$this->attributes[$key] = $value;
	}
	
	/**
	 * Deletes attribute by name.
	 * 
	 * @param string $key
	 * @return void
	 */
	public function removeAttribute($key) {
		unset($this->attributes[$key]);
	}
	
	/**
	 * Checks if attribute exists.
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function isAttribute($key) {
		return isset($this->attributes[$key]);
	}
	
	/**
	 * Decapsulates attributes as array.
	 * 
	 * @return array
	 */
	public function toArray() {
		return $this->attributes;
	}
	
	/**
	 * Checks if component has no attributes.
	 * 
	 * @return boolean
	 */
	public function isEmpty() {
		return empty($this->attributes);
	}
}