<?php

declare(strict_types=1);

namespace Tests\Http\Data;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\MoneyBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class MoneyBuilderTest extends TestCase
{
    #[Test]
    #[DataProvider('minorUnitsProvider')]
    public function it_creates_money_from_minor_units(string $currency, int $amount, string $expectedCurrency, string $expectedValue): void
    {
        $money = Money::of($currency)->minorUnits($amount);

        $this->assertSame($expectedCurrency, $money->currency);
        $this->assertSame($expectedValue, $money->value);
    }

    public static function minorUnitsProvider(): array
    {
        return [
            'EUR cents' => ['EUR', 1000, 'EUR', '10.00'],
            'lowercase JPY' => ['jpy', 1000, 'JPY', '1000'],
            'BHD three decimals' => ['BHD', 1000, 'BHD', '1.000'],
            'negative refund' => ['EUR', -1000, 'EUR', '-10.00'],
            'zero' => ['EUR', 0, 'EUR', '0.00'],
        ];
    }

    #[Test]
    #[DataProvider('stringProvider')]
    public function it_creates_money_from_a_decimal_string(string $currency, string $value, string $expectedCurrency): void
    {
        $money = Money::of($currency)->fromString($value);

        $this->assertSame($expectedCurrency, $money->currency);
        $this->assertSame($value, $money->value);
    }

    public static function stringProvider(): array
    {
        return [
            'EUR' => ['EUR', '10.00', 'EUR'],
            'lowercase EUR' => ['eur', '10.00', 'EUR'],
        ];
    }

    #[Test]
    public function builder_is_readonly(): void
    {
        $reflection = new ReflectionClass(MoneyBuilder::class);

        $this->assertTrue($reflection->isReadOnly());
    }
}
