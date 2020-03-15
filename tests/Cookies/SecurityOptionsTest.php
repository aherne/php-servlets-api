<?php
namespace Test\Lucinda\STDOUT\Cookies;
    
use Lucinda\STDOUT\Cookies\SecurityOptions;
use Lucinda\UnitTest\Result;

class SecurityOptionsTest
{
    private $object;
        
    public function __construct()
    {
        $this->object = new SecurityOptions();
    }

    public function setExpiredTime()
    {
        $this->object->setExpiredTime(10);
        return new Result(true);
    }
        

    public function getExpiredTime()
    {
        return new Result($this->object->getExpiredTime()==time()+10);
    }
        

    public function setSecuredByHTTPS()
    {
        $this->object->setSecuredByHTTPS(true);
        return new Result(true);
    }
        

    public function isSecuredByHTTPS()
    {
        return new Result($this->object->isSecuredByHTTPS());
    }
        

    public function setSecuredByHTTPheaders()
    {
        $this->object->setSecuredByHTTPheaders(true);
        return new Result(true);
    }
        

    public function isSecuredByHTTPheaders()
    {
        return new Result($this->object->isSecuredByHTTPheaders());
    }
        

    public function setPath()
    {
        $this->object->setPath("/foo/");
        return new Result(true);
    }
        

    public function getPath()
    {
        return new Result($this->object->getPath()=="/foo/");
    }
        

    public function setDomain()
    {
        $this->object->setDomain("www.example.com");
        return new Result(true);
    }
        

    public function getDomain()
    {
        return new Result($this->object->getDomain()=="www.example.com");
    }
        

}
