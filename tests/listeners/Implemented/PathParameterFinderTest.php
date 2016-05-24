<?php
require_once(dirname(dirname(__DIR__))."/bootstrap.php");
require_once(getSourceFileName(__FILE__));

class PathParameterFinderTest extends PHPUnit_Framework_TestCase {
	public function testPPFT1() {
		$objPPF = new PathParameterFinder("a/{b}/{c}", "a/1/2");
		$this->assertTrue($objPPF->isFound());
		$this->assertEquals("a", $objPPF->getPath());
		$this->assertEquals(array("b"=>1,"c"=>2), $objPPF->getParameters());
	}
	public function testPPFT2() {
		$objPPF = new PathParameterFinder("a/{b}", "a/1/2");
		$this->assertFalse($objPPF->isFound());
	}
	public function testPPFT3() {
		$objPPF = new PathParameterFinder("a/{b}/{c}", "a/1");
		$this->assertFalse($objPPF->isFound());
	}
	public function testPPFT4() {
		$objPPF = new PathParameterFinder("a/{b}/{c}", "a1/1/2");
		$this->assertFalse($objPPF->isFound());
	}
}