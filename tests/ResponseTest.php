<?php
namespace Test\Lucinda\STDOUT;

use Lucinda\STDOUT\Response;
use Lucinda\UnitTest\Result;

class ResponseTest
{
    private $object;
    
    public function __construct()
    {
        $this->object = new Response("text/html", "index");
    }
    
    public function setStatus()
    {
        $this->object->setStatus(404);
        return new Result(true);
    }
    
    
    public function getStatus()
    {
        return new Result($this->object->getStatus()->getId()==404);
    }
    
    
    public function view()
    {
        return new Result($this->object->view()->getFile()=="index");
    }
    
    
    public function headers()
    {
        $this->object->headers("Authorization", "Bearer asdf");
        return new Result($this->object->headers()["Authorization"]=="Bearer asdf");
    }
    
    
    public function setBody()
    {
        $this->object->setBody("asd");
        return new Result(true);
    }
    
    
    public function getBody()
    {
        return new Result($this->object->getBody()=="asd");
    }
    
    
    public function redirect()
    {
        return new Result(false, "Redirection cannot be unit tested!");
    }
    
    
    public function commit()
    {
        ob_start();
        $this->object->commit();
        $result = ob_get_contents();
        ob_end_clean();
        return new Result($result=="asd");
    }
}
