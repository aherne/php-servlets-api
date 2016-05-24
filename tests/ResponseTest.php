<?php
require_once("bootstrap.php");
require_once(getSourceFileName(__FILE__));
$strSourcesFolder = dirname(dirname(__FILE__));
require_once($strSourcesFolder."/src/Runnable.php");
require_once($strSourcesFolder."/src/response/Wrapper.php");
require_once($strSourcesFolder."/src/response/Implemented/ViewWrapper.php");
require_once($strSourcesFolder."/src/exceptions/ServletException.php");
class ResponseTest extends PHPUnit_Framework_TestCase {
	public function testResponse() {
		$objResponse = new Response();
		$objResponse->setCharacterEncoding("UTF8");	
		$objResponse->setContentType("text/html");
		$objResponse->setHeader("Accept-Language","en-US");
		$objResponse->setStatus(ResponseStatuses::SC_OK); 
		$objResponse->setAttribute("lucinda", "10");
		$objResponse->setView(dirname(__FILE__)."/view");
		$objResponse->build("ViewWrapper");
		$objResponse->commit();
		$this->expectOutputString("10");
	}
}