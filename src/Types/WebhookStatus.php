<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum WebhookStatus: string
{
    case Enabled = 'enabled';
    case Blocked = 'blocked';
    case Disabled = 'disabled';
    case Deleted = 'deleted';
}
