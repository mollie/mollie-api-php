<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum SessionStatus: string
{
    case Open = 'open';
    case Expired = 'expired';
    case Completed = 'completed';
}
