<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class DisputeResolved extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::DISPUTE_RESOLVED;
    }
}
