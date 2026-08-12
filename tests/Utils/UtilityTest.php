<?php

declare(strict_types=1);

namespace Tests\Utils;

use Mollie\Api\Types\PaymentStatus;
use Mollie\Api\Utils\Utility;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UtilityTest extends TestCase
{
    #[DataProvider('classBasenameProvider')]
    #[Test]
    public function class_basename($input, string $expected): void
    {
        $this->assertSame($expected, Utility::classBasename($input));
    }

    public static function classBasenameProvider(): array
    {
        return [
            'fqcn string' => ['Mollie\\Api\\Resources\\Payment', 'Payment'],
            'single segment' => ['Payment', 'Payment'],
            'object instance' => [new \stdClass(), 'stdClass'],
        ];
    }

    #[DataProvider('equalsProvider')]
    #[Test]
    public function equals_handles_enums_and_strings($value, $expected, bool $matches): void
    {
        $this->assertSame($matches, Utility::equals($value, $expected));
    }

    public static function equalsProvider(): array
    {
        return [
            'enum matches enum' => [PaymentStatus::Paid, PaymentStatus::Paid, true],
            'string matches enum value' => ['paid', PaymentStatus::Paid, true],
            'string matches string constant' => ['active', 'active', true],
            'enum does not match different enum' => [PaymentStatus::Open, PaymentStatus::Paid, false],
            'string does not match enum value' => ['open', PaymentStatus::Paid, false],
        ];
    }
}
