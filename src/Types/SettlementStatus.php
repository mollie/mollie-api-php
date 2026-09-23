<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum SettlementStatus: string
{
    case Open = 'open';
    case Pending = 'pending';
    case Paidout = 'paidout';
    case Failed = 'failed';
}
