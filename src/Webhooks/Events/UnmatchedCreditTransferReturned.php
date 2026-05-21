<?php

declare(strict_types=1);

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class UnmatchedCreditTransferReturned extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::UNMATCHED_CREDIT_TRANSFER_RETURNED;
    }
}
