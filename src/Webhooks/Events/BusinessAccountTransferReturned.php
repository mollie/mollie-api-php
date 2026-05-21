<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class BusinessAccountTransferReturned extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::BUSINESS_ACCOUNT_TRANSFER_RETURNED;
    }
}
