<?php
require_once("/tests/UnitTest.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Runnable.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/AttributesFactory.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Response.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Response/Wrapper.php");
require_once("/libraries/suites/MVC/ServletsAPI/classes/Response/Implemented/ViewWrapper.php");
require_once("/libraries/suites/MVC/ServletsAPI/exceptions/ServletException.php");

class ResponseTest extends UnitTest {
	protected function service() {
		$objResponse = new Response();
		$objResponse->setCharacterEncoding("UTF8");	
		$objResponse->setContentType("text/html");
		$objResponse->setHeader("Accept-Language","en-US");
		$objResponse->setStatus(ResponseStatuses::SC_OK); 
		$objResponse->setAttribute("lucinda", "10");
		$objResponse->build("ViewWrapper");
		$objResponse->commit();
	}
}

new ResponseTest();