<?php
require_once("/tests/UnitTest.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/AttributesFactory.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Application.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Request.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Runnable.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Listeners/RequestListener.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Listeners/Implemented/PageValidatorListener.php");
require_once("/libraries/suites/MVC/ServletsAPI/exceptions/ServletException.php");
require_once("/libraries/suites/MVC/ServletsAPI/exceptions/ServletApplicationException.php");

class PageValidatorListenerTest extends UnitTest {
	protected function service() {
		// create configuration object
		$objApplication = new Application("D:\workspace_php\Libraries\configuration.xml");

		// create request object
		$_SERVER = array(
				"REMOTE_ADDR"=>"193.60.168.69",
				"REMOTE_PORT"=>"5390",
				"SERVER_ADDR"=>"217.112.82.20",
				"SERVER_PORT"=>"80",
				"SERVER_NAME"=>"localhost",
				"HTTP_X_FORWARDED_FOR"=>"example",
				"REQUEST_METHOD"=>"GET",
		
				"HTTP_HOST"=>"www.test.com",
				"REQUEST_URI"=>"/servlets/?a=b&c=d",
				"DOCUMENT_ROOT"=>"",
				"SCRIPT_FILENAME"=>"/servlets/",
				"SCRIPT_URL"=>"/servlets/",
				"QUERY_STRING"=>"a=b&c=d",
		);
		$_GET = array("a"=>"b", "c"=>"d");
		$_POST = array("z"=>"e");
		$_FILES = array();
		$objRequest = new Request();
		
		// now test class
		$objPVL = new PageValidatorListener($objApplication, $objRequest);
		$objPVL->run();
		
		// test 1
		$this->assertTrue(__LINE__, $objRequest->getAttribute("page_url"), "index");
		$this->assertTrue(__LINE__, $objRequest->getAttribute("page_extension"), "html");
		$this->assertTrue(__LINE__, $objRequest->getAttribute("page_content_type"), "text/html");
		
		// now test again using normal values
		$_SERVER['REQUEST_URI'] = "/servlets/test/zaza.json?a=b&c=d";
		$_SERVER['SCRIPT_URL'] = "/servlets/test/zaza.json";
		$objRequest = new Request();
		
		// now test class
		$objPVL = new PageValidatorListener($objApplication, $objRequest);
		$objPVL->run();
		
		// test 1
		$this->assertTrue(__LINE__, $objRequest->getAttribute("page_url"), "test/zaza");
		$this->assertTrue(__LINE__, $objRequest->getAttribute("page_extension"), "json");
		$this->assertTrue(__LINE__, $objRequest->getAttribute("page_content_type"), "application/json");
	}
}

new PageValidatorListenerTest();