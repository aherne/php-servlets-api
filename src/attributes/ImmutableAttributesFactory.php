<?php
namespace Lucinda\MVC\STDOUT;

require_once("ImmutableAttributes.php");
require_once("AttributesFactory.php");

/**
 * Implements a factory of immutable attributes
 */
class ImmutableAttributesFactory implements ImmutableAttributes, AttributesFactory {
    /**
     * @var array
     */
    protected $attributes =  array();
    
    /**
     * Populates attributes from the beginning.
     * 
     * @param array $attributes
     */
    public function __construct($attributes) {
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
     * Checks if attribute exists.
     *
     * @param string $key
     * @return boolean
     */
    public function contains($key) {
        return isset($this->attributes[$key]);
    }
    
    /**
     * Gets all attributes as array.
     *
     * @return array
     */
    public function toArray() {
        return $this->attributes;
    }
    
    /**
     * Checks if factory has no attributes.
     *
     * @return boolean
     */
    public function isEmpty() {
        return empty($this->attributes);
    }
}