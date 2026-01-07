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
            'euro' => ['euro', '10.00', 'EUR', '10.00'],
            'usd' => ['usd', '25.50', 'USD', '25.50'],
        ];
    }

    /**
     * @test
     * @dataProvider fromStringValidProvider
     */
    public function it_can_parse_money_from_string(string $input, string $expectedCurrency, string $expectedValue): void
    {
        $money = Money::fromString($input);

        $this->assertEquals($expectedCurrency, $money->currency);
        $this->assertEquals($expectedValue, $money->value);
    }

    public function fromStringValidProvider(): array
    {
        return [
            'currency first' => ['EUR 10.00', 'EUR', '10.00'],
            'currency last' => ['10.00 EUR', 'EUR', '10.00'],
            'case insensitive' => ['usd 25.50', 'USD', '25.50'],
            'whitespace around' => ['  EUR 10.00  ', 'EUR', '10.00'],
            'no decimal places' => ['EUR 10', 'EUR', '10'],
            'multiple decimal places' => ['EUR 10.12345', 'EUR', '10.12345'],
            'GBP currency first' => ['GBP 100.00', 'GBP', '100.00'],
            'JPY currency first' => ['JPY 100.00', 'JPY', '100.00'],
            'CAD currency first' => ['CAD 100.00', 'CAD', '100.00'],
            'AUD currency first' => ['AUD 100.00', 'AUD', '100.00'],
            'CHF currency first' => ['CHF 100.00', 'CHF', '100.00'],
            'currency last without decimal' => ['100 USD', 'USD', '100'],
        ];
    }

    /**
     * @test
     * @dataProvider fromStringInvalidProvider
     */
    public function it_throws_exception_for_invalid_string_format(string $input, ?string $expectedMessage = null): void
    {
        $this->expectException(\InvalidArgumentException::class);
        if ($expectedMessage !== null) {
            $this->expectExceptionMessage($expectedMessage);
        }

        Money::fromString($input);
    }

    public function fromStringInvalidProvider(): array
    {
        return [
            'invalid format' => [
                'invalid',
                "Invalid money string format: 'invalid'. Expected format: 'EUR 10.00' or '10.00 EUR'",
            ],
            'empty string' => [
                '',
                "Invalid money string format: ''. Expected format: 'EUR 10.00' or '10.00 EUR'",
            ],
            'whitespace only' => [
                '   ',
                "Invalid money string format: ''. Expected format: 'EUR 10.00' or '10.00 EUR'",
            ],
            'missing space' => ['EUR10.00'],
            'invalid currency format' => ['EU 10.00'],
            'invalid value format' => ['EUR abc'],
        ];
    }
}
