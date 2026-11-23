<?php

declare(strict_types=1);

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class RefundFailed extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::REFUND_FAILED;
    }
}
