<?php
require_once("/tests/UnitTest.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/AttributesFactory.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Application.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Runnable.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Response/Wrapper.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Locators/WrapperLocator.php");
require_once("/libraries/suites/MVC/ServletsAPI/exceptions/ServletException.php");
require_once("/libraries/suites/MVC/ServletsAPI/exceptions/ServletApplicationException.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Response/Implemented/ViewWrapper.php");

class WrapperLocatorTest extends UnitTest {
	protected function service() {
		$objApplication = new Application("D:\workspace_php\Libraries\configuration.xml");
		// test default wrapper
		$objCL = new WrapperLocator($objApplication,"x");
		$this->assertTrue(__LINE__, $objCL->getClassName(), "ViewWrapper");
		// test default wrapper
		$objCL = new WrapperLocator($objApplication,"application/json");
		$this->assertTrue(__LINE__, $objCL->getClassName(), "JsonWrapper");
	}
}

new WrapperLocatorTest();