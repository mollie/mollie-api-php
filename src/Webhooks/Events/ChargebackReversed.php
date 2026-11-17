<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class ChargebackReversed extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::CHARGEBACK_REVERSED;
    }
}
