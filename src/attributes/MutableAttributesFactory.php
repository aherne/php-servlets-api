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
     * {@inheritDoc}
     * @see ImmutableAttributes::get()
     */
    public function get($key) {
        return (isset($this->attributes[$key])?$this->attributes[$key]:null);
    }
    
    /**
     * {@inheritDoc}
     * @see MutableAttributes::set()
     */
    public function set($key, $value) {
        $this->attributes[$key] = $value;
    }
    
    /**
     * {@inheritDoc}
     * @see MutableAttributes::remove()
     */
    public function remove($key) {
        unset($this->attributes[$key]);
    }
    
    /**
     * {@inheritDoc}
     * @see ImmutableAttributes::contains()
     */
    public function contains($key) {
        return isset($this->attributes[$key]);
    }
    
    /**
     * {@inheritDoc}
     * @see AttributesFactory::toArray()
     */
    public function toArray() {
        return $this->attributes;
    }
    
    /**
     * {@inheritDoc}
     * @see AttributesFactory::isEmpty()
     */
    public function isEmpty() {
        return empty($this->attributes);
    }
}