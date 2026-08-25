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
    #[DataProvider('isTrueProvider')]
    #[Test]
    public function is_true_matches_api_boolean_coercion(mixed $value, bool $expected): void
    {
        $this->assertSame($expected, Utility::isTrue($value));
    }

    public static function isTrueProvider(): array
    {
        return [
            'boolean true' => [true, true],
            'boolean false' => [false, false],
            'integer one' => [1, true],
            'integer zero' => [0, false],
            'integer two' => [2, false],
            'negative integer' => [-1, false],
            'float one' => [1.0, true],
            'float one point five' => [1.5, false],
            'numeric string one' => ['1', true],
            'numeric string zero' => ['0', false],
            'numeric string two' => ['2', false],
            'negative numeric string' => ['-1', false],
            'decimal numeric string' => ['1.5', false],
            'uppercase true' => ['TRUE', true],
            'titlecase true' => ['True', true],
            'lowercase true' => ['true', true],
            'uppercase on' => ['ON', true],
            'titlecase on' => ['On', true],
            'lowercase on' => ['on', true],
            'uppercase yes' => ['YES', true],
            'lowercase yes' => ['yes', true],
            'uppercase false' => ['FALSE', false],
            'lowercase false' => ['false', false],
            'uppercase off' => ['OFF', false],
            'titlecase off' => ['Off', false],
            'lowercase off' => ['off', false],
            'lowercase no' => ['no', false],
            'empty string' => ['', false],
            'invalid string' => ['maybe', false],
            'leading whitespace one' => [' 1', true],
            'trailing whitespace true' => ['true ', true],
            'leading whitespace true' => [' true', true],
            'tab prefixed true' => ["\ttrue", true],
            'leading whitespace zero' => [' 0', false],
            'uppercase no' => ['NO', false],
            'titlecase no' => ['No', false],
            'zero padded one' => ['01', false],
            'float zero' => [0.0, false],
            'null' => [null, false],
            'array' => [[], false],
            'standard object' => [new \stdClass(), false],
            'stringable object' => [new class {
                public function __toString(): string
                {
                    return 'true';
                }
            }, false],
            'resource' => [fopen('php://memory', 'r'), false],
            'closure' => [static fn () => true, false],
        ];
    }

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
