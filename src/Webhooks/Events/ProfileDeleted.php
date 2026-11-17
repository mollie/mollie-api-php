<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class ProfileDeleted extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::PROFILE_DELETED;
    }
}
