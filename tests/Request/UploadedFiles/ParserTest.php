<?php
namespace Test\Lucinda\STDOUT\Request\UploadedFiles;
    
use Lucinda\STDOUT\Request\UploadedFiles\Parser;
use Lucinda\UnitTest\Result;

class ParserTest
{

    public function getResult()
    {
        /**

    [file1] => Array
        (
            [name] => MyFile.txt (comes from the browser, so treat as tainted)
            [type] => text/plain  (not sure where it gets this from - assume the browser, so treat as tainted)
            [tmp_name] => /tmp/php/php1h4j1o (could be anywhere on your system, depending on your config settings, but the user has no control, so this isn't tainted)
            [error] => UPLOAD_ERR_OK  (= 0)
            [size] => 123   (the size in bytes)
        )
         */
        $_FILES = [
            "test"=> [
                "name"=>"testfile",
                "type"=>"text/plain",
                "tmp_name"=>dirname(__DIR__, 3)."/testfile.txt",
                "size"=>123456,
                "error"=>0
            ]
        ];
        $parser = new Parser();
        $result = $parser->getResult();
        return new Result(isset($result["test"]["size"]) && $result["test"]["size"]==123456);
    }
        

}
