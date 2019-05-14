<?php

namespace Mollie\Api\Types;

class MandateMethod
{
    const DIRECTDEBIT = "directdebit";
    const CREDITCARD = "creditcard";

    public static function getForFirstPaymentMethod($firstPaymentMethod)
    {
        if(in_array($firstPaymentMethod, [
            PaymentMethod::APPLEPAY,
            PaymentMethod::CREDITCARD,
        ])) {
            return static::CREDITCARD;
        }

        return static::DIRECTDEBIT;
    }
}