<?php
namespace Lucinda\MVC\STDOUT;

require_once("ImmutableAttributes.php");

interface MutableAttributes extends ImmutableAttributes
{
    function set($key, $value);
    function remove($key);
}

