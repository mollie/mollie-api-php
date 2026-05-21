<?php

declare(strict_types=1);

namespace Mollie\Api\Types\Includes;

use Mollie\Api\Types\PaymentIncludesQuery;

enum RefundEmbed: string
{
    case Payment = PaymentIncludesQuery::PAYMENT;
}
