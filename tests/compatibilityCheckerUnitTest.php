<?php

class Mollie_API_CompatibilityCheckerUnitTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Mollie_API_CompatibilityChecker|PHPUnit_Framework_MockObject_MockObject
	 */
	protected $checker;

	protected function setUp()
	{
		parent::setUp();

		$this->checker = $this->getMock("Mollie_API_CompatibilityChecker", array("satisfiesPhpVersion", "satisfiesJsonExtension", "satisfiesCurlExtension", "satisfiesCurlFunctions"));
	}

	/**
	 * @expectedException Mollie_API_Exception_IncompatiblePlatform
	 * @expectedExceptionCode Mollie_API_Exception_IncompatiblePlatform::INCOMPATIBLE_PHP_VERSION
	 */
	public function testCheckCompatibilityThrowsExceptionOnPhpVersion ()
	{
		$this->checker->expects($this->once())
			->method("satisfiesPhpVersion")
			->will($this->returnValue(FALSE)); // Fail

		$this->checker->expects($this->never())
			->method("satisfiesJsonExtension");

		$this->checker->expects($this->never())
			->method("satisfiesCurlExtension");

		$this->checker->expects($this->never())
			->method("satisfiesCurlFunctions");

		$this->checker->checkCompatibility();
	}

	/**
	 * @expectedException Mollie_API_Exception_IncompatiblePlatform
	 * @expectedExceptionCode Mollie_API_Exception_IncompatiblePlatform::INCOMPATIBLE_JSON_EXTENSION
	 */
	public function testCheckCompatibilityThrowsExceptionOnJsonExtension ()
	{
		$this->checker->expects($this->once())
			->method("satisfiesPhpVersion")
			->will($this->returnValue(TRUE));

		$this->checker->expects($this->once())
			->method("satisfiesJsonExtension")
			->will($this->returnValue(FALSE)); // Fail

		$this->checker->expects($this->never())
			->method("satisfiesCurlExtension");

		$this->checker->expects($this->never())
			->method("satisfiesCurlFunctions");

		$this->checker->checkCompatibility();
	}

	/**
	 * @expectedException Mollie_API_Exception_IncompatiblePlatform
	 * @expectedExceptionCode Mollie_API_Exception_IncompatiblePlatform::INCOMPATIBLE_CURL_EXTENSION
	 */
	public function testCheckCompatibilityThrowsExceptionOnCurlExtension ()
	{
		$this->checker->expects($this->once())
			->method("satisfiesPhpVersion")
			->will($this->returnValue(TRUE));

		$this->checker->expects($this->once())
			->method("satisfiesJsonExtension")
			->will($this->returnValue(TRUE));

		$this->checker->expects($this->once())
			->method("satisfiesCurlExtension")
			->will($this->returnValue(FALSE)); // Fail

		$this->checker->expects($this->never())
			->method("satisfiesCurlFunctions");

		$this->checker->checkCompatibility();
	}

	/**
	 * @expectedException Mollie_API_Exception_IncompatiblePlatform
	 * @expectedExceptionCode Mollie_API_Exception_IncompatiblePlatform::INCOMPATIBLE_CURL_FUNCTION
	 */
	public function testCheckCompatibilityThrowsExceptionOnCurlFunctions ()
	{
		$this->checker->expects($this->once())
			->method("satisfiesPhpVersion")
			->will($this->returnValue(TRUE));

		$this->checker->expects($this->once())
			->method("satisfiesJsonExtension")
			->will($this->returnValue(TRUE));

		$this->checker->expects($this->once())
			->method("satisfiesCurlExtension")
			->will($this->returnValue(TRUE));

		$this->checker->expects($this->once())
			->method("satisfiesCurlFunctions")
			->will($this->returnValue(FALSE)); // Fail

		$this->checker->checkCompatibility();
	}
}