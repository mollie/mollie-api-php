<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class ChargebackReceived extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::CHARGEBACK_RECEIVED;
    }
}
