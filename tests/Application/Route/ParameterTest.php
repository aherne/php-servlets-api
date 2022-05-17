<?php

namespace Test\Lucinda\STDOUT\Application\Route;

use Lucinda\STDOUT\Application\Route\Parameter;
use Lucinda\UnitTest\Result;

class ParameterTest
{
    private $object;

    public function __construct()
    {
        $this->object = new Parameter(simplexml_load_string('
        <parameter name="id" validator="UserIdValidator" mandatory="1"/>
        '));
    }


    public function getName()
    {
        return new Result($this->object->getName()=="id");
    }


    public function getValidator()
    {
        return new Result($this->object->getValidator()=="UserIdValidator");
    }


    public function isMandatory()
    {
        return new Result($this->object->isMandatory());
    }
}
