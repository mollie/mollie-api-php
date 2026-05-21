<?php

declare(strict_types=1);

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class FileAccepted extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::FILE_ACCEPTED;
    }
}
