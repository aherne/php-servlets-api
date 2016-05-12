<?php
require_once("/tests/UnitTest.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/AttributesFactory.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Request.php");
class RequestTest extends UnitTest {
	protected function service() {
		// mockup
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
		
		// begin testing
		$objRequest = new Request();
		$this->assertTrue(__LINE__, $objRequest->getClient()->getIP(), "193.60.168.69");
		$this->assertTrue(__LINE__, $objRequest->getClient()->getPort(), "5390");
		// getCookie
		$this->assertTrue(__LINE__, $objRequest->getHeaders()["X_FORWARDED_FOR"], "example");
		// getInputStream
		$this->assertTrue(__LINE__, $objRequest->getMethod(), "GET");
		$this->assertTrue(__LINE__, sizeof($objRequest->getParameters()), 1);
		$this->assertTrue(__LINE__, $objRequest->getServer()->getIP(), "217.112.82.20");
		$this->assertTrue(__LINE__, $objRequest->getServer()->getPort(), "80");
		$this->assertTrue(__LINE__, $objRequest->getServer()->getName(), "localhost");
		// getSession
		// getUploadedFiles
		$this->assertTrue(__LINE__, $objRequest->getURI()->getContextPath(), "/servlets/");
		$this->assertTrue(__LINE__, $objRequest->getURI()->getHost(), "www.test.com");
		$this->assertTrue(__LINE__, $objRequest->getURI()->getPageExtension(), "html");
		$this->assertTrue(__LINE__, $objRequest->getURI()->getPagePath(), "test");
		$this->assertTrue(__LINE__, sizeof($objRequest->getURI()->getParameters()), 2);
		$this->assertTrue(__LINE__, $objRequest->getURI()->getProtocol(), "http");
		$this->assertTrue(__LINE__, $objRequest->getURI()->getQueryString(), "a=b&c=d");
		$this->assertTrue(__LINE__, $objRequest->getURI()->getURL(), "http://www.test.com/servlets/test.html?a=b&c=d");
	}
}
new RequestTest();
