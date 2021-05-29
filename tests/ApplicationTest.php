<?php
namespace Test\Lucinda\STDOUT;

use Lucinda\STDOUT\Application;
use Lucinda\UnitTest\Result;

class ApplicationTest
{
    private $object;
    
    public function __construct()
    {
        $this->object = new Application(__DIR__."/mocks/configuration.xml");
    }

    public function getDefaultRoute()
    {
        return new Result($this->object->getDefaultRoute()=="index");
    }
        

    public function getDefaultFormat()
    {
        return new Result($this->object->getDefaultFormat()=="html");
    }
    
    public function getViewsPath()
    {
        return new Result($this->object->getViewsPath()=="tests/mocks/views");
    }
       
    public function getVersion()
    {
        return new Result($this->object->getVersion()=="1.0.0");
    }

    public function getTag()
    {
        return new Result($this->object->getTag("formats")!==null);
    }
        

    public function routes()
    {
        return new Result($this->object->routes("users")!==null);
    }
        

    public function resolvers()
    {
        return new Result($this->object->resolvers("html")!==null);
    }


    public function getXML()
    {
        return new Result($this->object->getXML() instanceof \SimpleXMLElement);
    }
    
    public function getSessionOptions()
    {
        return new Result($this->object->getSessionOptions()->isSecuredByHTTPS());
    }
        

    public function getCookieOptions()
    {
        return new Result($this->object->getCookieOptions()->isSecuredByHTTPS());
    }
}
