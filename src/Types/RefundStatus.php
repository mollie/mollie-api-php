<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum RefundStatus: string
{
    case Queued = 'queued';
    case Pending = 'pending';
    case Processing = 'processing';
    case Refunded = 'refunded';
    case Failed = 'failed';
    case Canceled = 'canceled';
}
