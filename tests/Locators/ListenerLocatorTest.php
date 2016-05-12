<?php
require_once("/tests/UnitTest.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/AttributesFactory.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Application.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Runnable.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Listeners/ApplicationListener.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Locators/ListenerLocator.php");
require_once("/libraries/suites/MVC/ServletsAPI/exceptions/ServletException.php");
require_once("/libraries/suites/MVC/ServletsAPI/exceptions/ServletApplicationException.php");

class ListenerLocatorTest extends UnitTest {
	protected function service() {
		$objApplication = new Application("D:\workspace_php\Libraries\configuration.xml");
		$objCL = new ListenerLocator($objApplication);
		
		$this->assertTrue(__LINE__, $objCL->getClassNames("ApplicationListener")[0], "MyApplicationListener");
	}
}

new ListenerLocatorTest();