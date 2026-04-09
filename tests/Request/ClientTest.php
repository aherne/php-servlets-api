<?php

namespace Test\Lucinda\STDOUT\Request;

use Lucinda\STDOUT\Request\Client;
use Lucinda\UnitTest\Validator\Integers;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class ClientTest
{
    private Client $object;

    public function __construct()
    {
        $this->object = new Client(TestHelper::requestServer());
    }

    public function getName()
    {
        return new Strings($this->object->getName())->assertEquals("client.local");
    }

    public function getIP()
    {
        return new Strings($this->object->getIP())->assertEquals("127.0.0.2");
    }

    public function getPort()
    {
        return new Integers($this->object->getPort())->assertEquals(59300);
    }
}
