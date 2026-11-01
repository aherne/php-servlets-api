<?php
namespace Test\Lucinda\STDOUT;

use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class SessionTest
{
    private \Lucinda\STDOUT\Session $object;

    public function __construct()
    {
        TestHelper::resetSessionState();
        $this->object = new \Lucinda\STDOUT\Session(TestHelper::application()->getSessionOptions());
    }

    public function start()
    {
        return new Booleans($this->object->start())->assertTrue();
    }

    public function isStarted()
    {
        return new Booleans($this->object->isStarted())->assertTrue();
    }

    public function set()
    {
        $this->object->set("asd", "fgh");
        return new Strings($_SESSION["asd"])->assertEquals("fgh");
    }

    public function get()
    {
        return new Strings($this->object->get("asd"))->assertEquals("fgh");
    }

    public function contains()
    {
        return new Booleans($this->object->contains("asd"))->assertTrue();
    }

    public function remove()
    {
        $this->object->remove("asd");
        return new Booleans($this->object->contains("asd"))->assertFalse();
    }

    public function destroy()
    {
        return new Booleans($this->object->destroy())->assertTrue();
    }

    public function abort()
    {
        $this->object->start();
        return new Booleans($this->object->abort())->assertTrue();
    }

    public function commit()
    {
        $this->object->start();
        return new Booleans($this->object->commit())->assertTrue();
    }

    public function cookie()
    {
        return new Objects($this->object->cookie())->assertInstanceOf(\Lucinda\STDOUT\Session\Cookie::class);
    }
    /**
     * Checks that clearing removes all entries while retaining a usable session
     */
    public function clear()
    {
        if (!$this->object->isStarted()) {
            $this->object->start();
        }
        $sessionID = $this->object->cookie()->getID();
        $this->object->set("authentication", ["id" => 7]);
        $this->object->set("application", "cart");
        $this->object->clear();

        $results = [
            new Booleans($_SESSION === [])->assertTrue("All session entries are removed"),
            new Booleans($this->object->isStarted())->assertTrue("Session remains active"),
            new Strings($this->object->cookie()->getID())->assertEquals($sessionID, "Session ID remains unchanged")
        ];
        $this->object->set("after_clear", "usable");
        $results[] = new Strings($this->object->get("after_clear"))->assertEquals("usable", "Session remains writable");
        $this->object->remove("after_clear");
        return $results;
    }
        

}
