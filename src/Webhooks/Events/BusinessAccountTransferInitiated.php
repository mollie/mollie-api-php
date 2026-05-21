<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class BusinessAccountTransferInitiated extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::BUSINESS_ACCOUNT_TRANSFER_INITIATED;
    }
}
