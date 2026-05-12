<?php

namespace Tests\Types;

use Mollie\Api\Types\PaymentMethod;
use PHPUnit\Framework\TestCase;

class PaymentMethodTest extends TestCase
{
    /** @test */
    public function google_pay_matches_api_method_name()
    {
        $this->assertSame('googlepay', PaymentMethod::GOOGLEPAY);
    }
}
