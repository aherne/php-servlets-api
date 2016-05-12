<?php
require_once("/tests/UnitTest.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/FrontController.php");
require_once("/libraries/suites/MVC/ServletsAPI/exceptions/ServletException.php");
require_once("/libraries/suites/MVC/ServletsAPI/exceptions/ServletApplicationException.php");

class FrontControllerTest extends UnitTest {
	protected function service() {
		// mockup
		define("DOCUMENT_DESCRIPTOR", "D:/workspace_php/Libraries/configuration.xml");
		$_SERVER = array(
				"REMOTE_ADDR"=>"193.60.168.69",
				"REMOTE_PORT"=>"5390",
				"SERVER_ADDR"=>"217.112.82.20",
				"SERVER_PORT"=>"80",
				"SERVER_NAME"=>"localhost",
				"HTTP_X_FORWARDED_FOR"=>"example",
				"REQUEST_METHOD"=>"GET",
		
				"HTTP_HOST"=>"www.test.com",
				"REQUEST_URI"=>"/servlets/test.html?a=b&c=d",
				"DOCUMENT_ROOT"=>"",
				"SCRIPT_FILENAME"=>"/servlets/",
				"SCRIPT_URL"=>"/servlets/test.html",
				"QUERY_STRING"=>"a=b&c=d",
		);
		$_GET = array("a"=>"b", "c"=>"d");
		$_POST = array("z"=>"e");
		$_FILES = array();
		new FrontController();
	}
}

new FrontControllerTest();