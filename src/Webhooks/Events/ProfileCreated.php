<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class ProfileCreated extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::PROFILE_CREATED;
    }
}
