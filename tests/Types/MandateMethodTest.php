<?php

declare(strict_types=1);

namespace Tests\Types;

use Mollie\Api\Types\MandateMethod;
use Mollie\Api\Types\PaymentMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MandateMethodTest extends TestCase
{
    /**
     * @param  string  $firstPaymentMethod
     * @param  string  $expectedMethod
     *
     */
    #[DataProvider('dpTestGetForFirstPaymentMethod')]
    #[Test]
    public function get_for_first_payment_method($firstPaymentMethod, $expectedMethod)
    {
        $actualMethod = MandateMethod::getForFirstPaymentMethod($firstPaymentMethod);
        $this->assertEquals($expectedMethod, $actualMethod);
    }

    public static function dpTestGetForFirstPaymentMethod()
    {
        return [
            [PaymentMethod::Applepay->value, MandateMethod::Creditcard->value],
            [PaymentMethod::Bacs->value, MandateMethod::Bacs->value],
            [PaymentMethod::Creditcard->value, MandateMethod::Creditcard->value],
            [PaymentMethod::Bancontact->value, MandateMethod::Directdebit->value],
            [PaymentMethod::Belfius->value, MandateMethod::Directdebit->value],
            [PaymentMethod::Eps->value, MandateMethod::Directdebit->value],
            [PaymentMethod::Giropay->value, MandateMethod::Directdebit->value],
            [PaymentMethod::Ideal->value, MandateMethod::Directdebit->value],
            [PaymentMethod::Inghomepay->value, MandateMethod::Directdebit->value],
            [PaymentMethod::Kbc->value, MandateMethod::Directdebit->value],
            [PaymentMethod::Sofort->value, MandateMethod::Directdebit->value],
            [PaymentMethod::Paypal->value, MandateMethod::Paypal->value],
        ];
    }
}