<?php

namespace Test\Lucinda\STDOUT\Request\UploadedFiles;

use Lucinda\STDOUT\Request\UploadedFiles\File;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Files;
use Lucinda\UnitTest\Validator\Integers;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class FileTest
{
    private File $object;
    private string $path;

    public function __construct()
    {
        $this->path = TestHelper::tempFile("uploaded.txt", "content");
        $this->object = new File([
            "name" => "uploaded.txt",
            "type" => "text/plain",
            "tmp_name" => $this->path,
            "size" => 7,
            "error" => 0,
        ]);
    }

    public function getName()
    {
        return new Strings($this->object->getName())->assertEquals("uploaded.txt");
    }

    public function getLocation()
    {
        return new Strings($this->object->getLocation())->assertEquals($this->path);
    }

    public function getContentType()
    {
        return new Strings($this->object->getContentType())->assertEquals("text/plain");
    }

    public function getSize()
    {
        return new Integers($this->object->getSize())->assertEquals(7);
    }

    public function move()
    {
        return new Booleans($this->object->move(TestHelper::fixturesPath("moved.txt")))->assertFalse();
    }

    public function delete()
    {
        $this->object->delete();
        return new Files($this->path)->assertNotExists();
    }
}
