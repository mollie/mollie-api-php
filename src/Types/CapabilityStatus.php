<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum CapabilityStatus: string
{
    case Enabled = 'enabled';
    case Pending = 'pending';
    case Disabled = 'disabled';
}
