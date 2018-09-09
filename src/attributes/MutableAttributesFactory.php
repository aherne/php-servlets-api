<?php
namespace Lucinda\MVC\STDOUT;

require_once("MutableAttributes.php");
require_once("AttributesFactory.php");

/**
 * Implements a factory of mutable attributes
 */
class MutableAttributesFactory implements MutableAttributes, AttributesFactory {
    /**
     * Property that must be set by children of class.
     *
     * @var array
     */
    protected $attributes =  array();
    
    /**
     * Populates attributes from the beginning, if found.
     *
     * @param array $attributes
     */
    public function __construct($attributes = array()) {
        $this->attributes = $attributes;
    }
    
    
    /**
     * Gets attribute by name.
     *
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return (isset($this->attributes[$key])?$this->attributes[$key]:null);
    }
    
    /**
     * Sets attribute by name & value.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->attributes[$key] = $value;
    }
    
    /**
     * Deletes attribute by name.
     *
     * @param string $key
     * @return void
     */
    public function remove($key) {
        unset($this->attributes[$key]);
    }
    
    /**
     * Checks if attribute exists.
     *
     * @param string $key
     * @return boolean
     */
    public function contains($key) {
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