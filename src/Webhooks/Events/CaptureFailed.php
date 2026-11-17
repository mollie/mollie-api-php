<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class CaptureFailed extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::CAPTURE_FAILED;
    }
}
