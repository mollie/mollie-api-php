<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

class PaymentIncludesQuery
{
    const PAYMENT = 'payment';

    const INCLUDES = [
        self::PAYMENT,
    ];
}
