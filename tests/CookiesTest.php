<?php
namespace Test\Lucinda\STDOUT;

use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class CookiesTest
{
    private \Lucinda\STDOUT\Cookies $object;

    public function __construct()
    {
        $_COOKIE = [];
        $this->object = new \Lucinda\STDOUT\Cookies(TestHelper::application()->getCookieOptions());
    }

    public function set()
    {
        $this->object->set("token", "abc123", 60);
        return new Strings($_COOKIE["token"])->assertEquals("abc123");
    }

    public function get()
    {
        return new Strings($this->object->get("token"))->assertEquals("abc123");
    }

    public function contains()
    {
        return new Booleans($this->object->contains("token"))->assertTrue();
    }

    public function remove()
    {
        $this->object->remove("token");
        return new Booleans(isset($_COOKIE["token"]))->assertFalse();
    }
}
