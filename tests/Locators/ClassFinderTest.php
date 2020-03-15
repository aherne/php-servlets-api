<?php
namespace Test\Lucinda\STDOUT\Locators;
    
use Lucinda\STDOUT\Locators\ClassFinder;
use Lucinda\UnitTest\Result;

class ClassFinderTest
{

    public function find()
    {
        $finder = new ClassFinder(dirname(__DIR__)."/mocks/validators");
        return new Result($finder->find("UserNameValidator")=="UserNameValidator");
    }
        

}
