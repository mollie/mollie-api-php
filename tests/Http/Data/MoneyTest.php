<?php

namespace Tests\Http\Data;

use Mollie\Api\Http\Data\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    /**
     * @test
     * @dataProvider fromArrayProvider
     */
    public function it_can_be_created_from_array(array $data, string $expectedCurrency, string $expectedValue): void
    {
        $money = Money::fromArray($data);

        $this->assertEquals($expectedCurrency, $money->currency);
        $this->assertEquals($expectedValue, $money->value);
    }

    public function fromArrayProvider(): array
    {
        return [
            'EUR' => [['currency' => 'EUR', 'value' => '10.00'], 'EUR', '10.00'],
            'USD' => [['currency' => 'USD', 'value' => '25.50'], 'USD', '25.50'],
            'GBP' => [['currency' => 'GBP', 'value' => '99.99'], 'GBP', '99.99'],
        ];
    }

    /**
     * @test
     * @dataProvider toArrayProvider
     */
    public function it_can_be_converted_to_array(string $currency, string $value, array $expected): void
    {
        $money = new Money($currency, $value);
        $array = $money->toArray();

        $this->assertEquals($expected, $array);
    }

    public function toArrayProvider(): array
    {
        return [
            'USD' => ['USD', '25.50', ['currency' => 'USD', 'value' => '25.50']],
            'EUR' => ['EUR', '10.00', ['currency' => 'EUR', 'value' => '10.00']],
            'GBP' => ['GBP', '99.99', ['currency' => 'GBP', 'value' => '99.99']],
        ];
    }

    /**
     * @test
     * @dataProvider reversibilityProvider
     */
    public function from_array_and_to_array_are_reversible(array $originalData): void
    {
        $money = Money::fromArray($originalData);
        $convertedData = $money->toArray();

        $this->assertEquals($originalData, $convertedData);
    }

    public function reversibilityProvider(): array
    {
        return [
            'GBP' => [['currency' => 'GBP', 'value' => '99.99']],
            'EUR' => [['currency' => 'EUR', 'value' => '10.00']],
            'USD' => [['currency' => 'USD', 'value' => '25.50']],
        ];
    }

    /**
     * @test
     * @dataProvider convenienceMethodProvider
     */
    public function it_can_create_money_with_convenience_methods(string $method, string $value, string $expectedCurrency, string $expectedValue): void
    {
        $money = Money::$method($value);

        $this->assertEquals($expectedCurrency, $money->currency);
        $this->assertEquals($expectedValue, $money->value);
    }

    public function convenienceMethodProvider(): array
    {
        return [
            'aed' => ['aed', '10.00', 'AED', '10.00'],
            'aud' => ['aud', '10.00', 'AUD', '10.00'],
            'bgn' => ['bgn', '10.00', 'BGN', '10.00'],
            'brl' => ['brl', '10.00', 'BRL', '10.00'],
            'cad' => ['cad', '10.00', 'CAD', '10.00'],
            'chf' => ['chf', '10.00', 'CHF', '10.00'],
            'czk' => ['czk', '10.00', 'CZK', '10.00'],
            'dkk' => ['dkk', '10.00', 'DKK', '10.00'],
            'euro' => ['euro', '10.00', 'EUR', '10.00'],
            'gbp' => ['gbp', '10.00', 'GBP', '10.00'],
            'hkd' => ['hkd', '10.00', 'HKD', '10.00'],
            'huf' => ['huf', '10.00', 'HUF', '10.00'],
            'ils' => ['ils', '10.00', 'ILS', '10.00'],
            'isk' => ['isk', '10.00', 'ISK', '10.00'],
            'jpy' => ['jpy', '10.00', 'JPY', '10.00'],
            'mxn' => ['mxn', '10.00', 'MXN', '10.00'],
            'myr' => ['myr', '10.00', 'MYR', '10.00'],
            'nok' => ['nok', '10.00', 'NOK', '10.00'],
            'nzd' => ['nzd', '10.00', 'NZD', '10.00'],
            'php' => ['php', '10.00', 'PHP', '10.00'],
            'pln' => ['pln', '10.00', 'PLN', '10.00'],
            'ron' => ['ron', '10.00', 'RON', '10.00'],
            'rub' => ['rub', '10.00', 'RUB', '10.00'],
            'sek' => ['sek', '10.00', 'SEK', '10.00'],
            'sgd' => ['sgd', '10.00', 'SGD', '10.00'],
            'thb' => ['thb', '10.00', 'THB', '10.00'],
            'twd' => ['twd', '10.00', 'TWD', '10.00'],
            'usd' => ['usd', '25.50', 'USD', '25.50'],
            'zar' => ['zar', '10.00', 'ZAR', '10.00'],
        ];
    }
}
