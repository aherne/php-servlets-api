<?php

namespace Test\Lucinda\STDOUT\Request;

use Lucinda\STDOUT\Request\Server;
use Lucinda\UnitTest\Validator\Integers;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class ServerTest
{
    private Server $object;

    public function __construct()
    {
        $this->object = new Server(TestHelper::requestServer());
    }

    public function getName()
    {
        return new Strings($this->object->getName())->assertEquals("www.documentation.local");
    }

    public function getIP()
    {
        return new Strings($this->object->getIP())->assertEquals("127.0.0.1");
    }

    public function getPort()
    {
        return new Integers($this->object->getPort())->assertEquals(8080);
    }

    public function getEmail()
    {
        return new Strings($this->object->getEmail())->assertEquals("admin@test.local");
    }

    public function getSoftware()
    {
        return new Strings($this->object->getSoftware())->assertEquals("PHP Built-In Server");
    }
}
