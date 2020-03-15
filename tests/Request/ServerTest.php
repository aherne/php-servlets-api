<?php
namespace Test\Lucinda\STDOUT\Request;
    
use Lucinda\STDOUT\Request\Server;
use Lucinda\UnitTest\Result;

class ServerTest
{
    private $object;
    
    public function __construct()
    {
        $_SERVER = [
            "SERVER_NAME"=>"www.example.com",
            "SERVER_ADDR"=>"127.0.0.1",
            "SERVER_PORT"=>80,
            "SERVER_ADMIN"=>"admin@example.com",
            'SERVER_SOFTWARE' => 'Apache/2.4.29 (Ubuntu)',
        ];
        $this->object = new Server();
    }     

    public function getName()
    {
        return new Result($this->object->getName()=="www.example.com");
    }
        

    public function getIP()
    {
        return new Result($this->object->getIP()=="127.0.0.1");
    }
        

    public function getPort()
    {
        return new Result($this->object->getPort()==80);
    }
        

    public function getEmail()
    {
        return new Result($this->object->getEmail()=="admin@example.com");
    }
        

    public function getSoftware()
    {
        return new Result($this->object->getSoftware()=="Apache/2.4.29 (Ubuntu)");
    }
        

}
