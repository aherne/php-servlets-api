<?php

namespace Test\Lucinda\STDOUT\Request;

use Lucinda\STDOUT\Request\URI;
use Lucinda\UnitTest\Result;

class URITest
{
    private $object;

    public function __construct()
    {
        $server = [
            'REQUEST_URI' => '/user/lucian',
            'REQUEST_METHOD' => 'GET',
            'DOCUMENT_ROOT' => '/var/www/html/documentation',
            'SCRIPT_FILENAME' => '/var/www/html/documentation/index.php',
            'QUERY_STRING' =>'asd=fgh'
        ];
        $this->object = new URI($server);
    }

    public function getContextPath()
    {
        return new Result($this->object->getContextPath()=="");
    }


    public function getPage()
    {
        return new Result($this->object->getPage()=="user/lucian");
    }


    public function getQueryString()
    {
        return new Result($this->object->getQueryString()=="asd=fgh");
    }


    public function parameters()
    {
        return new Result($this->object->parameters("asd")=="fgh");
    }
}
