<?php
namespace Test\Lucinda\STDOUT;

use Lucinda\STDOUT\Application;
use Lucinda\UnitTest\Result;

class ApplicationTest
{
    private $object;
    
    public function __construct()
    {
        $this->object = new Application(__DIR__."/configuration.xml");
    }

    public function getDefaultPage()
    {
        return new Result($this->object->getDefaultPage()=="index");
    }
        

    public function getDefaultFormat()
    {
        return new Result($this->object->getDefaultFormat()=="html");
    }
        

    public function getControllersPath()
    {
        return new Result($this->object->getControllersPath()=="tests/mocks/controllers");
    }
    
    
    public function getValidatorsPath()
    {
        return new Result($this->object->getValidatorsPath()=="tests/mocks/validators");
    }
        

    public function getViewResolversPath()
    {
        return new Result($this->object->getViewResolversPath()=="tests/mocks/resolvers");
    }
        

    public function getViewsPath()
    {
        return new Result($this->object->getViewsPath()=="tests/mocks/views");
    }
        

    public function getAutoRouting()
    {
        return new Result(!$this->object->getAutoRouting());
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
        

    public function formats()
    {
        return new Result($this->object->formats("html")!==null);
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
