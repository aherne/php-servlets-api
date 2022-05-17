<?php

namespace Test\Lucinda\STDOUT\Application;

use Lucinda\STDOUT\Application\Route;
use Lucinda\STDOUT\Request\Method;
use Lucinda\UnitTest\Result;

class RouteTest
{
    private $object;


    public function __construct()
    {
        $this->object = new Route(simplexml_load_string('
      	<route id="user/(name)" controller="BlogController" view="blog" format="json" method="GET">
      		<parameter name="name" validator="UserNameValidator" mandatory="1"/>
      	</route>
        '));
    }

    public function getID()
    {
        return new Result($this->object->getID()=="user/(name)");
    }


    public function getController()
    {
        return new Result($this->object->getController()=="BlogController");
    }


    public function getView()
    {
        return new Result($this->object->getView()=="blog");
    }


    public function getFormat()
    {
        return new Result($this->object->getFormat()=="json");
    }


    public function getValidRequestMethod()
    {
        return new Result($this->object->getValidRequestMethod()==Method::GET);
    }


    public function getValidParameters()
    {
        return new Result(!empty($this->object->getValidParameters()));
    }
}
