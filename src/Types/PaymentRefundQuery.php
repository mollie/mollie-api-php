<?php

namespace Mollie\Api\Types;

class PaymentRefundQuery
{
    const INCLUDE_PAYMENT = 'payment';

    const INCLUDES = [
        self::INCLUDE_PAYMENT,
    ];
}
