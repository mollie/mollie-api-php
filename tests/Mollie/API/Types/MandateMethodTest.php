<?php

namespace Tests\Mollie\API\Types;

use Mollie\Api\Types\MandateMethod;
use Mollie\Api\Types\PaymentMethod;
use PHPUnit\Framework\TestCase;

class MandateMethodTest extends TestCase
{
    /**
     * @param string $firstPaymentMethod
     * @param string $expectedMethod
     * @dataProvider dpTestGetForFirstPaymentMethod
     */
    public function testGetForFirstPaymentMethod($firstPaymentMethod, $expectedMethod)
    {
        $actualMethod = MandateMethod::getForFirstPaymentMethod($firstPaymentMethod);
        $this->assertEquals($expectedMethod, $actualMethod);
    }

    public function dpTestGetForFirstPaymentMethod()
    {
        return [
            [PaymentMethod::APPLEPAY, MandateMethod::CREDITCARD],
            [PaymentMethod::CREDITCARD, MandateMethod::CREDITCARD],
            [PaymentMethod::BANCONTACT, MandateMethod::DIRECTDEBIT],
            [PaymentMethod::BELFIUS, MandateMethod::DIRECTDEBIT],
            [PaymentMethod::EPS, MandateMethod::DIRECTDEBIT],
            [PaymentMethod::GIROPAY, MandateMethod::DIRECTDEBIT],
            [PaymentMethod::IDEAL, MandateMethod::DIRECTDEBIT],
            [PaymentMethod::INGHOMEPAY, MandateMethod::DIRECTDEBIT],
            [PaymentMethod::KBC, MandateMethod::DIRECTDEBIT],
            [PaymentMethod::SOFORT, MandateMethod::DIRECTDEBIT],
            [PaymentMethod::PAYPAL, MandateMethod::PAYPAL],
        ];
    }
}
