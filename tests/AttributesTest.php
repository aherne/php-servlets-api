<?php
namespace Test\Lucinda\STDOUT;

use Lucinda\STDOUT\Attributes;
use Lucinda\UnitTest\Result;

class AttributesTest
{
    private $object;
    
    public function __construct()
    {
        $this->object = new Attributes("tests/mocks/events");
    }
    

    public function getEventsFolder()
    {
        return new Result($this->object->getEventsFolder()=="tests/mocks/events");
    }
        

    public function setValidPage()
    {
        $this->object->setValidPage("info/(name)");
        return new Result(true);
    }
        

    public function getValidPage()
    {
        return new Result($this->object->getValidPage()=="info/(name)");
    }
        

    public function setPathParameters()
    {
        $this->object->setPathParameters(["name"=>"lucinda"]);
        return new Result(true);
    }
        

    public function getPathParameters()
    {
        return new Result($this->object->getPathParameters("name")=="lucinda");
    }
        

    public function setValidFormat()
    {
        $this->object->setValidFormat("html");
        return new Result(true);
    }
        

    public function getValidFormat()
    {
        return new Result($this->object->getValidFormat()=="html");
    }
        

    public function setValidParameters()
    {
        $this->object->setValidParameters(["name"=>1]);
        return new Result(true);
    }
        

    public function getValidParameters()
    {
        return new Result($this->object->getValidParameters("name")==1);
    }
}
