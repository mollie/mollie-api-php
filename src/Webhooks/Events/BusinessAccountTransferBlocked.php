<?php

declare(strict_types=1);

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class BusinessAccountTransferBlocked extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::BUSINESS_ACCOUNT_TRANSFER_BLOCKED;
    }
}
