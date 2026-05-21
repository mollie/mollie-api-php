<?php

declare(strict_types=1);

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class DisputeCreated extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::DISPUTE_CREATED;
    }
}
