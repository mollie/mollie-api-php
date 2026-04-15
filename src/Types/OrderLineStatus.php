<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum OrderLineStatus: string
{
    case Created = 'created';
    case Paid = 'paid';
    case Authorized = 'authorized';
    case Canceled = 'canceled';
    case Shipping = 'shipping';
    case Completed = 'completed';
}
