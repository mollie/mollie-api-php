<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class RefundPending extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::REFUND_PENDING;
    }
}
