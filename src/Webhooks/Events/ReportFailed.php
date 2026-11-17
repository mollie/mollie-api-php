<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class ReportFailed extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::REPORT_FAILED;
    }
}
