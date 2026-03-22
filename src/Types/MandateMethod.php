<?php

namespace Mollie\Api\Types;

class MandateMethod
{
    public const BACS = 'bacs';

    public const DIRECTDEBIT = 'directdebit';

    public const CREDITCARD = 'creditcard';

    public const PAYPAL = 'paypal';

    /**
     * @param  string  $firstPaymentMethod
     * @return string
     */
    public static function getForFirstPaymentMethod($firstPaymentMethod)
    {
        if ($firstPaymentMethod === PaymentMethod::PAYPAL) {
            return static::PAYPAL;
        }

        if ($firstPaymentMethod === PaymentMethod::BACS) {
            return static::BACS;
        }

        if (in_array($firstPaymentMethod, [
            PaymentMethod::APPLEPAY,
            PaymentMethod::CREDITCARD,
        ])) {
            return static::CREDITCARD;
        }

        return static::DIRECTDEBIT;
    }
}
