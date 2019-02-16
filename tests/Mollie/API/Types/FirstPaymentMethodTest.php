<?php

namespace Tests\Mollie\API\Types;

use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\FirstPaymentMethod;
use Mollie\Api\Types\PaymentMethod;
use PHPUnit\Framework\TestCase;

class FirstPaymentMethodTest extends TestCase
{
    public function testGetAll()
    {
        $this->assertEquals([
            'creditcard',
            'bancontact',
            'belfius',
            'eps',
            'giropay',
            'ideal',
            'inghomepay',
            'kbc',
            'sofort',
        ], FirstPaymentMethod::all());
    }

    public function testExists()
    {
        $this->assertTrue(FirstPaymentMethod::exists(PaymentMethod::CREDITCARD));
        $this->assertFalse(FirstPaymentMethod::exists(PaymentMethod::KLARNA_PAY_LATER));
    }
}