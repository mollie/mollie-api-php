<?php

namespace Tests\Factories;

use Mollie\Api\Factories\MoneyFactory;
use Mollie\Api\Http\Data\Money;
use PHPUnit\Framework\TestCase;

class MoneyFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_money_object()
    {
        $money = MoneyFactory::new([
            'currency' => 'EUR',
            'value' => '10.00',
        ])->create();

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals('EUR', $money->currency);
        $this->assertEquals('10.00', $money->value);
    }

    /** @test */
    public function create_throws_exception_for_invalid_data()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Money data provided');

        MoneyFactory::new([
            'currency' => 'EUR',
            // missing value
        ])->create();
    }
}
