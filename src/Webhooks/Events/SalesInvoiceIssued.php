<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class SalesInvoiceIssued extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::SALES_INVOICE_ISSUED;
    }
}
