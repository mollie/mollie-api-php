<?php

declare(strict_types=1);

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class ConnectBalanceTransferFailed extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::CONNECT_BALANCE_TRANSFER_FAILED;
    }
}
