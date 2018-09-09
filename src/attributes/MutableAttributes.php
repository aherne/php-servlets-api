<?php
namespace Lucinda\MVC\STDOUT;

require_once("ImmutableAttributes.php");

/**
 * Defines blueprint for attributes that remain mutable throughout application lifecycle.
 */
interface MutableAttributes extends ImmutableAttributes
{
    /**
     * Sets value of attribute by its key.
     * 
     * @param string $key
     * @param mixed $value
     */
    function set($key, $value);
    
    /**
     * Removes attribute by its key.
     * 
     * @param string $key
     */
    function remove($key);
}

