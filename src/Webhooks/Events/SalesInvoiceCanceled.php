<?php

declare(strict_types=1);

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class SalesInvoiceCanceled extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::SALES_INVOICE_CANCELED;
    }
}
