<?php

namespace Mollie\Api\Webhooks\Events;

use Mollie\Api\Webhooks\WebhookEventType;

class BusinessAccountDraftTransferApproved extends BaseEvent
{
    public static function type(): string
    {
        return WebhookEventType::BUSINESS_ACCOUNT_DRAFT_TRANSFER_APPROVED;
    }
}
