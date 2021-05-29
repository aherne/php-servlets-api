<?php
namespace Test\Lucinda\STDOUT\mocks\RouteValidators;

use Lucinda\STDOUT\EventListeners\Validators\ParameterValidator;

class UserNameValidator implements ParameterValidator
{
    private $users_db = ["lucian"=>1];
    
    public function validate($value)
    {
        return (isset($this->users_db[$value])?$this->users_db[$value]:null);
    }
}
