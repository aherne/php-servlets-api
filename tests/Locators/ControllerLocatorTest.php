<?php
require_once("/tests/UnitTest.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/AttributesFactory.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Application.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Runnable.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Controller.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Locators/ControllerLocator.php");
require_once("/libraries/suites/MVC/ServletsAPI/exceptions/ServletException.php");
require_once("/libraries/suites/MVC/ServletsAPI/exceptions/ServletApplicationException.php");

class ControllerLocatorTest extends UnitTest {
	protected function service() {
		$objApplication = new Application("D:\workspace_php\Libraries\configuration.xml");
		$objCL = new ControllerLocator($objApplication, "test");
		$this->assertTrue(__LINE__, $objCL->getClassName(), "TestController");
	}
}

new ControllerLocatorTest();