<?php

namespace Test\Lucinda\STDOUT\XmlTags;

use Lucinda\STDOUT\XmlTags\CookiesOptions;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class CookiesOptionsTest
{
    private CookiesOptions $object;

    public function __construct()
    {
        $this->object = new CookiesOptions(
            TestHelper::element('<cookies path="/" domain="example.com" https_only="1" headers_only="1" same_site="Lax"/>')
        );
    }

    public function getPath()
    {
        return new Strings($this->object->getPath())->assertEquals("/");
    }

    public function getDomain()
    {
        return new Strings($this->object->getDomain())->assertEquals("example.com");
    }

    public function isSecuredByHTTPS()
    {
        return new Booleans($this->object->isSecuredByHTTPS())->assertTrue();
    }

    public function isSecuredByHTTPheaders()
    {
        return new Booleans($this->object->isSecuredByHTTPheaders())->assertTrue();
    }
    public function getSameSite()
    {
        return new Strings($this->object->getSameSite())->assertEquals("Lax");
    }
        

}
