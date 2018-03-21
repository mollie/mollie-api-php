<?php

use Mollie\Api\CompatibilityChecker;

class Mollie_API_CompatibilityCheckerUnitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CompatibilityChecker|PHPUnit_Framework_MockObject_MockObject
     */
    protected $checker;

    protected function setUp()
    {
        parent::setUp();

        $this->checker = $this->getMockBuilder(CompatibilityChecker::class)
            ->setMethods(array(
                "satisfiesPhpVersion",
                "satisfiesJsonExtension",
                "satisfiesCurlExtension",
                "satisfiesCurlFunctions",
            ))
            ->getMock();
    }

    /**
     * @expectedException Mollie\Api\Exceptions\IncompatiblePlatform
     * @expectedExceptionCode Mollie\Api\Exceptions\IncompatiblePlatform::INCOMPATIBLE_PHP_VERSION
     */
    public function testCheckCompatibilityThrowsExceptionOnPhpVersion()
    {
        $this->checker->expects($this->once())
            ->method("satisfiesPhpVersion")
            ->will($this->returnValue(false)); // Fail

        $this->checker->expects($this->never())
            ->method("satisfiesJsonExtension");

        $this->checker->expects($this->never())
            ->method("satisfiesCurlExtension");

        $this->checker->expects($this->never())
            ->method("satisfiesCurlFunctions");

        $this->checker->checkCompatibility();
    }

    /**
     * @expectedException Mollie\Api\Exceptions\IncompatiblePlatform
     * @expectedExceptionCode Mollie\Api\Exceptions\IncompatiblePlatform::INCOMPATIBLE_JSON_EXTENSION
     */
    public function testCheckCompatibilityThrowsExceptionOnJsonExtension()
    {
        $this->checker->expects($this->once())
            ->method("satisfiesPhpVersion")
            ->will($this->returnValue(true));

        $this->checker->expects($this->once())
            ->method("satisfiesJsonExtension")
            ->will($this->returnValue(false)); // Fail

        $this->checker->expects($this->never())
            ->method("satisfiesCurlExtension");

        $this->checker->expects($this->never())
            ->method("satisfiesCurlFunctions");

        $this->checker->checkCompatibility();
    }
}
