<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class PaymentPending extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::PAYMENT_PENDING;
    }
}
