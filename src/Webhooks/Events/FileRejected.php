<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class FileRejected extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::FILE_REJECTED;
    }
}
