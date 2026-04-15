<?php

declare(strict_types=1);

namespace Tests\Http\Data;

use InvalidArgumentException;
use Mollie\Api\Http\Data\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MoneyFromMinorUnitsTest extends TestCase
{
    #[DataProvider('minorUnitsProvider')]
    public function test_it_creates_money_from_minor_units(string $currency, int $amount, string $expectedValue): void
    {
        $money = Money::fromMinorUnits($currency, $amount);

        $this->assertSame(strtoupper($currency), $money->currency);
        $this->assertSame($expectedValue, $money->value);
    }

    public static function minorUnitsProvider(): array
    {
        return [
            'EUR 1000 cents = 10.00' => ['EUR', 1000, '10.00'],
            'EUR 1 cent = 0.01' => ['EUR', 1, '0.01'],
            'EUR 0 = 0.00' => ['EUR', 0, '0.00'],
            'JPY has 0 decimals' => ['JPY', 1000, '1000'],
            'BHD has 3 decimals' => ['BHD', 1000, '1.000'],
            'BHD smallest unit' => ['BHD', 1, '0.001'],
            'negative refund' => ['EUR', -1234, '-12.34'],
            'lowercase currency normalised' => ['eur', 500, '5.00'],
        ];
    }

    public function test_it_throws_on_unsupported_currency(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported currency "XXX"');

        Money::fromMinorUnits('XXX', 100);
    }
}
