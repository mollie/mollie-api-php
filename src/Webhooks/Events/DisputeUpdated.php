<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class DisputeUpdated extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::DISPUTE_UPDATED;
    }
}
