<?php

namespace Tests;

use Mollie\Api\CompatibilityChecker;

class CompatibilityCheckerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CompatibilityChecker|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $checker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checker = $this->getMockBuilder(CompatibilityChecker::class)
            ->setMethods([
                'satisfiesPhpVersion',
                'satisfiesJsonExtension',
            ])
            ->getMock();
    }

    public function test_check_compatibility_throws_exception_on_php_version()
    {
        $this->expectException(\Mollie\Api\Exceptions\IncompatiblePlatformException::class);
        $this->checker->expects($this->once())
            ->method('satisfiesPhpVersion')
            ->will($this->returnValue(false)); // Fail

        $this->checker->expects($this->never())
            ->method('satisfiesJsonExtension');

        $this->checker->checkCompatibility();
    }

    public function test_check_compatibility_throws_exception_on_json_extension()
    {
        $this->expectException(\Mollie\Api\Exceptions\IncompatiblePlatformException::class);
        $this->checker->expects($this->once())
            ->method('satisfiesPhpVersion')
            ->will($this->returnValue(true));

        $this->checker->expects($this->once())
            ->method('satisfiesJsonExtension')
            ->will($this->returnValue(false)); // Fail

        $this->checker->checkCompatibility();
    }
}
