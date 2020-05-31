<?php
namespace Test\Lucinda\STDOUT\Request;

use Lucinda\STDOUT\Request\Client;
use Lucinda\UnitTest\Result;

class ClientTest
{
    private $object;
    
    public function __construct()
    {
        $_SERVER = [
            "REMOTE_HOST"=>"www.example.com",
            "REMOTE_ADDR"=>"127.0.0.1",
            "REMOTE_PORT"=>59300
        ];
        $this->object = new Client();
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
        return new Result($this->object->getPort()==59300);
    }
}
