<?php

namespace Test\Lucinda\STDOUT;

use Lucinda\STDOUT\Session;
use Lucinda\UnitTest\Result;

class SessionTest
{
    private $object;

    public function __construct()
    {
        $this->object = new Session();
    }

    public function start()
    {
        $this->object->start();
        return new Result(true);
    }


    public function isStarted()
    {
        return new Result($this->object->isStarted());
    }


    public function set()
    {
        $this->object->set("asd", "fgh");
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

    public function abort()
    {
        $this->object->start();
        return new Result($this->object->abort());
    }
        

    public function commit()
    {
        $this->object->start();
        $status = $this->object->commit();
        return new Result($status);
    }
        

    public function cookie()
    {
        return new Result(true, "Tested via Session::Cookie!");
    }


    public function destroy()
    {
        $status = $this->object->destroy();
        return new Result($status);
    }
}
