<?php

namespace Test\Lucinda\STDOUT\Session;

use Lucinda\STDOUT\Session\Options;
use Lucinda\UnitTest\Result;

class OptionsTest
{
    private $object;

    public function __construct()
    {
        $this->object = new Options(
            simplexml_load_string(
                '
        <session save_path="foo/bar" name="sessid_new" expired_time="1" expired_on_close="2" https_only="1" headers_only="1" referrer_check="qwerty" handler="MyHandler" auto_start="1"/>
        '
            )
        );
    }

    public function getSavePath()
    {
        return new Result($this->object->getSavePath()=="foo/bar");
    }


    public function getName()
    {
        return new Result($this->object->getName()=="sessid_new");
    }


    public function getExpiredTime()
    {
        return new Result($this->object->getExpiredTime()==1);
    }


    public function getExpiredOnBrowserClose()
    {
        return new Result($this->object->getExpiredOnBrowserClose()==2);
    }


    public function isSecuredByHTTPS()
    {
        return new Result($this->object->isSecuredByHTTPS());
    }


    public function isSecuredByHTTPheaders()
    {
        return new Result($this->object->isSecuredByHTTPheaders());
    }


    public function getReferrerCheck()
    {
        return new Result($this->object->getReferrerCheck()=="qwerty");
    }


    public function getHandler()
    {
        return new Result($this->object->getHandler()=="MyHandler");
    }


    public function isAutoStart()
    {
        return new Result($this->object->isAutoStart());
    }
}
