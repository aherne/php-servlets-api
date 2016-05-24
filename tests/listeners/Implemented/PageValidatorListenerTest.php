<?php
require_once(dirname(dirname(dirname(__DIR__)))."/src/FrontController.php");

class PageValidatorListenerTest extends PHPUnit_Framework_TestCase {
	public function testPVLT() {
		// create configuration object
		$objApplication = new Application(dirname(dirname(__DIR__))."/configuration.xml");
	
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
				"REQUEST_URI"=>"/servlets/caller?a=b&c=d",
				"DOCUMENT_ROOT"=>"",
				"SCRIPT_FILENAME"=>"/servlets/",
				"SCRIPT_URL"=>"/servlets/caller",
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
		$this->assertEquals("caller", $objRequest->getAttribute("page_url"));
		$this->assertEquals("html", $objRequest->getAttribute("page_extension"));
		$this->assertEquals("text/html", $objRequest->getAttribute("page_content_type"));
	
		// now test again using normal values
		$_SERVER['REQUEST_URI'] = "/servlets/caller.json/1/2";
		$_SERVER['SCRIPT_URL'] = "/servlets/caller.json/1/2";
		$objRequest = new Request();
	
		// now test class
		$objPVL = new PageValidatorListener($objApplication, $objRequest);
		$objPVL->run();
	
		// test 1
		$this->assertEquals("caller", $objRequest->getAttribute("page_url"));
		$this->assertEquals("json", $objRequest->getAttribute("page_extension"));
		$this->assertEquals("application/json", $objRequest->getAttribute("page_content_type"));
		$this->assertEquals(array("a"=>1,"b"=>2), $objRequest->getAttribute("path_parameters"));
	}
}