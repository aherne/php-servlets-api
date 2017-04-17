<?php
require_once("bootstrap.php");
require_once(getSourceFileName(__FILE__));
class RequestTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
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
	}
	
	public function testRequest() {
		$objRequest = new Request();
		$this->assertEquals("193.60.168.69", $objRequest->getClient()->getIP());
		$this->assertEquals("5390", $objRequest->getClient()->getPort());
		$this->assertEquals("example", $objRequest->getHeaders()["X_FORWARDED_FOR"]);
		$this->assertEquals("GET", $objRequest->getMethod());
		$this->assertEquals(array("z"=>"e"), $objRequest->getParameters());
		$this->assertEquals("217.112.82.20", $objRequest->getServer()->getIP());
		$this->assertEquals("80", $objRequest->getServer()->getPort());
		$this->assertEquals("localhost", $objRequest->getServer()->getName());
		$this->assertEquals("/servlets/", $objRequest->getURI()->getContextPath());
		$this->assertEquals("www.test.com", $objRequest->getURI()->getHost());
		$this->assertEquals("test.html", $objRequest->getURI()->getPage());
		$this->assertEquals(array("a"=>"b", "c"=>"d"), $objRequest->getURI()->getParameters());
		$this->assertEquals("http", $objRequest->getURI()->getProtocol());
		$this->assertEquals("a=b&c=d", $objRequest->getURI()->getQueryString());
		$this->assertEquals("http://www.test.com/servlets/test.html?a=b&c=d", $objRequest->getURI()->getURL());
		// getCookie
		// getSession
		// getInputStream
	}
	
	public function testUploadedFiles1() {
		$_FILES = unserialize('a:1:{s:1:"a";a:5:{s:4:"name";a:1:{i:0;a:2:{i:1;s:51:"12644838_1728862143999505_5178290272638334520_n.jpg";i:2;s:12:"linkedin.jpg";}}s:4:"type";a:1:{i:0;a:2:{i:1;s:10:"image/jpeg";i:2;s:10:"image/jpeg";}}s:8:"tmp_name";a:1:{i:0;a:2:{i:1;s:14:"/tmp/phpzVFQor";i:2;s:14:"/tmp/phpaRetfI";}}s:5:"error";a:1:{i:0;a:2:{i:1;i:0;i:2;i:0;}}s:4:"size";a:1:{i:0;a:2:{i:1;i:61028;i:2;i:16197;}}}}');
		$objRequest = new Request();
		$tblExpectedResult = array("a"=>array("0"=>array(
				1=>new UploadedFile(array(
						"name"=>"12644838_1728862143999505_5178290272638334520_n.jpg",
						"tmp_name"=>"/tmp/phpzVFQor",
						"type"=>"image/jpeg",
						"size"=>61028,
						"error"=>0
				)),
				2=>new UploadedFile(array(
						"name"=>"linkedin.jpg",
						"tmp_name"=>"/tmp/phpaRetfI",
						"type"=>"image/jpeg",
						"size"=>16197,
						"error"=>0
				))
		)));
		
		$this->assertEquals($tblExpectedResult, $objRequest->getUploadedFiles());
	}
	
	public function testUploadedFiles2() {
		$_FILES = array("a"=>array(
			"name" => "12644838_1728862143999505_5178290272638334520_n.jpg",
			"type" => "image/jpeg",
			"tmp_name" => "/tmp/phpzVFQor",
			"error" => 0,
			"size" => 61028
		));
		$objRequest = new Request();
		$tblExpectedResult = array("a"=>new UploadedFile(array(
				"name"=>"12644838_1728862143999505_5178290272638334520_n.jpg",
				"tmp_name"=>"/tmp/phpzVFQor",
				"type"=>"image/jpeg",
				"size"=>61028,
				"error"=>0
		)));
		$this->assertEquals($tblExpectedResult, $objRequest->getUploadedFiles());
	}
	
	public function tearDown() {
		$_SERVER = array();
		$_GET = array();
		$_POST = array();
		$_FILES = array();
	}
}