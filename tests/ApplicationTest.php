<?php
require_once("bootstrap.php");
require_once(getSourceFileName(__FILE__));
class ApplicationTest extends PHPUnit_Framework_TestCase {
	public function testApplication() {
		$objApplication = new Application(__DIR__."/configuration.xml");
		$this->assertFalse(false, $objApplication->getAutoRouting());
		$this->assertEquals("application/controllers", $objApplication->getControllersPath());
		$this->assertEquals("application/listeners", $objApplication->getListenersPath());
		$this->assertEquals("application/wrappers", $objApplication->getWrappersPath());
		$this->assertEquals("application/views", $objApplication->getViewsPath());
		$this->assertEquals("public", $objApplication->getPublicPath());
		$this->assertEquals("UTF-8", $objApplication->getDefaultCharacterEncoding());
		$this->assertEquals("html", $objApplication->getDefaultExtension());
		$this->assertEquals("index", $objApplication->getDefaultPage());
		$this->assertEquals(array("MyApplicationListener"), $objApplication->getListeners());
		$this->assertEquals(true, $objApplication->hasFormat("html"));
		$this->assertEquals(true, $objApplication->hasRoute("caller"));
		
		$objFormat1 = new Format("json","application/json","JsonWrapper");
		$this->assertEquals($objFormat1, $objApplication->getFormatInfo("json"));
		
		$objFormat2 = new Format("html","text/html");
		$this->assertEquals(array("json"=>$objFormat1,"html"=>$objFormat2), $objApplication->getFormats());
		
		$objRoute1 = new Route("caller","CallerController");
		$this->assertEquals($objRoute1, $objApplication->getRouteInfo("caller"));
		
		$objRoute2 = new Route("caller/{a}/{b}","TestController");
		$this->assertEquals(array("caller/{a}/{b}"=>$objRoute2,"caller"=>$objRoute1), $objApplication->getRoutes());
	}

	/**
	 * @expectedException     ApplicationException
	 */
	public function testException() {
		new Application("bad.xml");
	}
}