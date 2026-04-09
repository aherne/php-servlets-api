<?php

namespace Test\Lucinda\STDOUT\XmlTags;

use Lucinda\STDOUT\XmlTags\SessionOptions;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Integers;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class SessionOptionsTest
{
    private SessionOptions $object;

    public function __construct()
    {
        $this->object = new SessionOptions(
            TestHelper::element(
                '<session save_path="/tmp" name="TESTSESSID" expired_time="60" expired_on_close="120" https_only="1" headers_only="1" referrer_check="Chrome" handler="SessionHandler" auto_start="1"/>'
            )
        );
    }

    public function getSavePath()
    {
        return new Strings($this->object->getSavePath())->assertEquals("/tmp");
    }

    public function getName()
    {
        return new Strings($this->object->getName())->assertEquals("TESTSESSID");
    }

    public function getExpiredTime()
    {
        return new Integers($this->object->getExpiredTime())->assertEquals(60);
    }

    public function getExpiredOnBrowserClose()
    {
        return new Integers($this->object->getExpiredOnBrowserClose())->assertEquals(120);
    }

    public function isSecuredByHTTPS()
    {
        return new Booleans($this->object->isSecuredByHTTPS())->assertTrue();
    }

    public function isSecuredByHTTPheaders()
    {
        return new Booleans($this->object->isSecuredByHTTPheaders())->assertTrue();
    }

    public function getReferrerCheck()
    {
        return new Strings($this->object->getReferrerCheck())->assertEquals("Chrome");
    }

    public function getHandler()
    {
        return new Strings($this->object->getHandler())->assertEquals("SessionHandler");
    }

    public function isAutoStart()
    {
        return new Booleans($this->object->isAutoStart())->assertTrue();
    }
}
