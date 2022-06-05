<?php

namespace Test\Lucinda\STDOUT\Cookies;

use Lucinda\STDOUT\Cookies\Options;
use Lucinda\UnitTest\Result;

class OptionsTest
{
    private $object;

    public function __construct()
    {
        $this->object = new Options(
            simplexml_load_string(
                '
        <cookies path="foo/bar" domain="www.example.com" https_only="1" headers_only="1"/>
        '
            )
        );
    }

    public function getPath()
    {
        return new Result($this->object->getPath()=="foo/bar");
    }


    public function getDomain()
    {
        return new Result($this->object->getDomain()=="www.example.com");
    }


    public function isSecuredByHTTPS()
    {
        return new Result($this->object->isSecuredByHTTPS());
    }


    public function isSecuredByHTTPheaders()
    {
        return new Result($this->object->isSecuredByHTTPheaders());
    }
}
