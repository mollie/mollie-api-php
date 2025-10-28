<?php

namespace Tests\Utils;

use Mollie\Api\Utils\Utility;
use PHPUnit\Framework\TestCase;

class UtilityTest extends TestCase
{
    /**
     * @test
     * @dataProvider classBasenameProvider
     */
    public function class_basename($input, string $expected): void
    {
        $this->assertSame($expected, Utility::classBasename($input));
    }

    public function classBasenameProvider(): array
    {
        return [
            'fqcn string' => ['Mollie\\Api\\Resources\\Payment', 'Payment'],
            'single segment' => ['Payment', 'Payment'],
            'object instance' => [new \stdClass(), 'stdClass'],
        ];
    }
}
