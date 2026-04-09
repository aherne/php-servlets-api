<?php

namespace Test\Lucinda\STDOUT\Session;

use Lucinda\STDOUT\Session\Cookie;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class CookieTest
{
    private \Lucinda\STDOUT\Session $session;
    private Cookie $object;

    public function __construct()
    {
        TestHelper::resetSessionState();
        $this->session = new \Lucinda\STDOUT\Session();
        $this->object = new Cookie();
    }

    public function getName()
    {
        return new Strings($this->object->getName())->assertEquals("PHPSESSID");
    }

    public function getID()
    {
        $this->session->start();
        $id = $this->object->getID();
        $this->session->destroy();
        return new Strings($id)->assertNotEmpty();
    }

    public function regenerateID()
    {
        $this->session->start();
        $status = $this->object->regenerateID();
        $this->session->destroy();
        return new Booleans($status)->assertTrue();
    }

    public function createNewID()
    {
        $this->session->start();
        $status = $this->object->createNewID();
        $this->session->destroy();
        return new Booleans($status)->assertTrue();
    }
}
