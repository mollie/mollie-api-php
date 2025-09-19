<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class SalesInvoiceCreated extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::SALES_INVOICE_CREATED;
    }
}
