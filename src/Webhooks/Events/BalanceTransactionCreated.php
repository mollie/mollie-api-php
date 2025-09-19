<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class BalanceTransactionCreated extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::BALANCE_TRANSACTION_CREATED;
    }
}
