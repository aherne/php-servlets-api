<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Defines blueprints for a factory of attributes.
 */
interface AttributesFactory
{
    /**
     * Exports factory contents to an array.
     * 
     * @return array
     */
    public function toArray();
    
    /**
     * Checks if factory is empty of contents
     * 
     * @return boolean
     */
    public function isEmpty();
}

