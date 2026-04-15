<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum MandateMethod: string
{
    case Bacs = 'bacs';
    case Directdebit = 'directdebit';
    case Creditcard = 'creditcard';
    case Paypal = 'paypal';

    public static function getForFirstPaymentMethod(string $firstPaymentMethod): string
    {
        if ($firstPaymentMethod === PaymentMethod::Paypal->value) {
            return self::Paypal->value;
        }

        if ($firstPaymentMethod === PaymentMethod::Bacs->value) {
            return self::Bacs->value;
        }

        if (in_array($firstPaymentMethod, [
            PaymentMethod::Applepay->value,
            PaymentMethod::Creditcard->value,
        ], true)) {
            return self::Creditcard->value;
        }

        return self::Directdebit->value;
    }
}
