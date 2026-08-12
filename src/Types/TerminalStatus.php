<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum TerminalStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Inactive = 'inactive';
}
