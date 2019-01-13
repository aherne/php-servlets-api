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
     * {@inheritDoc}
     * @see ImmutableAttributes::get()
     */
    public function get($key) {
        return (isset($this->attributes[$key])?$this->attributes[$key]:null);
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