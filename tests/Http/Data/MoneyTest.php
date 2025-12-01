<?php

namespace Tests\Http\Data;

use Mollie\Api\Http\Data\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_from_array()
    {
        $data = [
            'currency' => 'EUR',
            'value' => '10.00',
        ];

        $money = Money::fromArray($data);

        $this->assertEquals('EUR', $money->currency);
        $this->assertEquals('10.00', $money->value);
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_array()
    {
        $money = new Money('USD', '25.50');

        $array = $money->toArray();

        $this->assertEquals('USD', $array['currency']);
        $this->assertEquals('25.50', $array['value']);
    }

    /**
     * @test
     */
    public function from_array_and_to_array_are_reversible()
    {
        $originalData = [
            'currency' => 'GBP',
            'value' => '99.99',
        ];

        $money = Money::fromArray($originalData);
        $convertedData = $money->toArray();

        $this->assertEquals($originalData, $convertedData);
    }
}
