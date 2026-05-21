<?php

declare(strict_types=1);

namespace Mollie\Api\Types\Includes;

use Mollie\Api\Types\PaymentQuery;

enum PaymentEmbed: string
{
    case Captures = PaymentQuery::EMBED_CAPTURES;
    case Refunds = PaymentQuery::EMBED_REFUNDS;
    case Chargebacks = PaymentQuery::EMBED_CHARGEBACKS;
}
