<?php
namespace Test\Lucinda\STDOUT\EventListeners\Validators;
    
use Lucinda\STDOUT\EventListeners\Validators\FormatValidator;
use Lucinda\STDOUT\Application;
use Lucinda\UnitTest\Result;

class FormatValidatorTest
{

    public function getFormat()
    {
        $validator = new FormatValidator(new Application(dirname(__DIR__, 2)."/configuration.xml"), "user/(name)");
        return new Result($validator->getFormat()=="json");
    }
        

}
