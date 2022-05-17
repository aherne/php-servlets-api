<?php

namespace Test\Lucinda\STDOUT\Request\UploadedFiles;

use Lucinda\STDOUT\Request\UploadedFiles\File;
use Lucinda\UnitTest\Result;

class FileTest
{
    private $object;

    public function __construct()
    {
        file_put_contents(dirname(__DIR__, 3)."/testfile.txt", "asdfg");
        $this->object = new File([
            "name"=>"testfile",
            "type"=>"text/plain",
            "tmp_name"=>dirname(__DIR__, 3)."/testfile.txt",
            "size"=>123456,
            "error"=>0
        ]);
    }

    public function getName()
    {
        return new Result($this->object->getName()=="testfile");
    }


    public function getLocation()
    {
        return new Result($this->object->getLocation()==dirname(__DIR__, 3)."/testfile.txt");
    }


    public function getContentType()
    {
        return new Result($this->object->getContentType()=="text/plain");
    }


    public function getSize()
    {
        return new Result($this->object->getSize()==123456);
    }


    public function move()
    {
        return new Result(false, "Moving uploaded file cannot be unit tested");
    }


    public function delete()
    {
        $this->object->delete();
        return new Result(!file_exists(dirname(__DIR__, 3)."/testfile.txt"));
    }
}
