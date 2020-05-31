<?php
namespace Test\Lucinda\STDOUT\Response;

use Lucinda\STDOUT\Response\View;
use Lucinda\UnitTest\Result;

class ViewTest
{
    private $object;
    
    public function __construct()
    {
        $this->object = new View("index");
    }

    public function setFile()
    {
        $this->object->setFile("admin");
        return new Result(true);
    }
        

    public function getFile()
    {
        return new Result($this->object->getFile()=="admin");
    }
        

    public function getData()
    {
        $this->object["test1"] = "me1";
        $this->object["test2"] = "me2";
        return new Result($this->object->getData()==["test1"=>"me1","test2"=>"me2"]);
    }

    public function offsetExists()
    {
        return new Result($this->object->offsetExists("test1"));
    }
        

    public function offsetGet()
    {
        return new Result($this->object->offsetGet("test1")=="me1");
    }
        

    public function offsetSet()
    {
        $this->object->offsetSet("test3", "me3");
        return new Result($this->object->offsetExists("test3"));
    }

    public function offsetUnset()
    {
        $this->object->offsetUnset("test3");
        return new Result(!$this->object->offsetExists("test3"));
    }
}
