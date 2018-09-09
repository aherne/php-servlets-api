<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Defines blueprint for attributes that, once set, become immutable later on.
 */
interface ImmutableAttributes
{
    /**
     * Gets attribute value by key.
     * 
     * @param string $key
     * @return mixed
     */
    function get($key);
    
    /**
     * Checks if an attribute exists by its key.
     * 
     * @param string $key
     * @return boolean
     */
    function contains($key);
}

