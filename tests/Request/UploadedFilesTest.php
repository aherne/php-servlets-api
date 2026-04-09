<?php

namespace Test\Lucinda\STDOUT\Request;

use Lucinda\STDOUT\Request\UploadedFiles;
use Lucinda\UnitTest\Validator\Objects;

class UploadedFilesTest
{
    public function toArray()
    {
        $files = [
            "a" => [
                "name" => "a.txt",
                "type" => "text/plain",
                "tmp_name" => "/tmp/a.txt",
                "error" => 0,
                "size" => 10,
            ],
            "b" => [
                "name" => [
                    "b1.txt",
                    "b2.txt",
                ],
                "type" => [
                    "text/plain",
                    "text/plain",
                ],
                "tmp_name" => [
                    "/tmp/b1.txt",
                    "/tmp/b2.txt",
                ],
                "error" => [
                    0,
                    0,
                ],
                "size" => [
                    20,
                    30,
                ],
            ],
            "d" => [
                "name" => [
                    "e" => [
                        "f" => "nested.txt",
                    ],
                ],
                "type" => [
                    "e" => [
                        "f" => "text/plain",
                    ],
                ],
                "tmp_name" => [
                    "e" => [
                        "f" => "/tmp/nested.txt",
                    ],
                ],
                "error" => [
                    "e" => [
                        "f" => 0,
                    ],
                ],
                "size" => [
                    "e" => [
                        "f" => 40,
                    ],
                ],
            ],
        ];
        $result = (new UploadedFiles($files))->toArray();

        return [
            (new Objects($result["a"]))->assertInstanceOf(\Lucinda\STDOUT\Request\UploadedFiles\File::class),
            (new Objects($result["b"][1]))->assertInstanceOf(\Lucinda\STDOUT\Request\UploadedFiles\File::class),
            (new Objects($result["d"]["e"]["f"]))->assertInstanceOf(\Lucinda\STDOUT\Request\UploadedFiles\File::class),
        ];
    }
}
