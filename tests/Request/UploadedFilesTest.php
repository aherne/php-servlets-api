<?php
namespace Test\Lucinda\STDOUT\Request;

use Lucinda\STDOUT\Request\UploadedFiles;
use Lucinda\UnitTest\Result;

class UploadedFilesTest
{
    public function toArray()
    {
        $_FILES = [
            "test"=> [
                "name"=>"testfile",
                "type"=>"text/plain",
                "tmp_name"=>dirname(__DIR__, 3)."/testfile.txt",
                "size"=>123456,
                "error"=>0
            ]
        ];
        $uploadedFiles = new UploadedFiles();
        $results = $uploadedFiles->toArray();
        return new Result(isset($results["test"]) && $results["test"]->getSize()==123456);
    }
}
