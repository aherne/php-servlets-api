<?php

namespace Test\Lucinda\STDOUT;

use Lucinda\STDOUT\Cookies;
use Lucinda\UnitTest\Result;

class CookiesTest
{
    private $object;

    public function __construct()
    {
        $this->object = new Cookies();
    }


    public function set()
    {
        $this->object->set("asd", "fgh", 10);
        return new Result(true);
    }


    public function get()
    {
        return new Result($this->object->get("asd")=="fgh");
    }


    public function contains()
    {
        return new Result($this->object->contains("asd"));
    }


    public function remove()
    {
        $this->object->remove("asd");
        return new Result(!$this->object->contains("asd"));
    }
}
