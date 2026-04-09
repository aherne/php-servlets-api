<?php
namespace Test\Lucinda\STDOUT;

use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Objects;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class RequestTest
{
    private \Lucinda\STDOUT\Request $object;

    public function __construct()
    {
        $filePath = TestHelper::tempFile("request-upload.txt", "upload");
        $this->object = TestHelper::request(
            TestHelper::requestServer([
                "REQUEST_URI" => "/user/lucian?asd=fgh",
                "QUERY_STRING" => "asd=fgh",
            ]),
            ["asd" => "fgh"],
            [],
            [
                "test" => [
                    "name" => "request-upload.txt",
                    "type" => "text/plain",
                    "tmp_name" => $filePath,
                    "size" => 6,
                    "error" => 0,
                ],
            ]
        );
    }

    public function getClient()
    {
        return new Objects($this->object->getClient())->assertInstanceOf(\Lucinda\STDOUT\Request\Client::class);
    }

    public function getServer()
    {
        return new Objects($this->object->getServer())->assertInstanceOf(\Lucinda\STDOUT\Request\Server::class);
    }

    public function getURI()
    {
        return new Objects($this->object->getURI())->assertInstanceOf(\Lucinda\STDOUT\Request\URI::class);
    }

    public function headers()
    {
        $headers = $this->object->headers();
        return [
            (new Arrays($headers))->assertContainsKey("User-Agent"),
            (new Strings($this->object->headers("User-Agent")))->assertEquals("Lucinda Test Agent"),
        ];
    }

    public function parameters()
    {
        return new Strings($this->object->parameters("asd"))->assertEquals("fgh");
    }

    public function uploadedFiles()
    {
        return new Objects($this->object->uploadedFiles("test"))->assertInstanceOf(
            \Lucinda\STDOUT\Request\UploadedFiles\File::class
        );
    }

    public function getMethod()
    {
        return new Strings($this->object->getMethod()->value)->assertEquals("GET");
    }

    public function getProtocol()
    {
        return new Strings($this->object->getProtocol()->value)->assertEquals("http");
    }

    public function getInputStream()
    {
        return new Strings($this->object->getInputStream())->assertEquals("");
    }
}
