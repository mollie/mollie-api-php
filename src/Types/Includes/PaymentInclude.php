<?php

declare(strict_types=1);

namespace Mollie\Api\Types\Includes;

use Mollie\Api\Types\PaymentQuery;

enum PaymentInclude: string
{
    case QrCode = PaymentQuery::INCLUDE_QR_CODE;
    case RemainderDetails = PaymentQuery::INCLUDE_REMAINDER_DETAILS;
}
