<?php

declare(strict_types=1);

namespace Tests;

use Mollie\Api\CompatibilityChecker;
use PHPUnit\Framework\Attributes\Test;

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
            ->onlyMethods([
                'satisfiesPhpVersion',
                'satisfiesJsonExtension',
            ])
            ->getMock();
    }

    #[Test]
    public function minimum_php_version_matches_v4_composer_requirement()
    {
        $this->assertSame('8.2', CompatibilityChecker::MIN_PHP_VERSION);
    }

    #[Test]
    public function check_compatibility_throws_exception_on_php_version()
    {
        $this->expectException(\Mollie\Api\Exceptions\IncompatiblePlatformException::class);
        $this->checker->expects($this->once())
            ->method('satisfiesPhpVersion')
            ->willReturn(false); // Fail

        $this->checker->expects($this->never())
            ->method('satisfiesJsonExtension');

        $this->checker->checkCompatibility();
    }

    #[Test]
    public function check_compatibility_throws_exception_on_json_extension()
    {
        $this->expectException(\Mollie\Api\Exceptions\IncompatiblePlatformException::class);
        $this->checker->expects($this->once())
            ->method('satisfiesPhpVersion')
            ->willReturn(true);

        $this->checker->expects($this->once())
            ->method('satisfiesJsonExtension')
            ->willReturn(false); // Fail

        $this->checker->checkCompatibility();
    }
}
