<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum CapabilityStatus: string
{
    case Unrequested = 'unrequested';
    case Enabled = 'enabled';
    case Pending = 'pending';
    case Disabled = 'disabled';
}
