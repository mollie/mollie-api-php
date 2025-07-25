<?php

namespace Mollie\Api\Webhooks;

class WebhookEventType
{
    /**
     * Payment Link event types
     */
    public const PAYMENT_LINK_PAID = 'payment-link.paid';

    /**
     * Profile event types
     */
    public const PROFILE_CREATED = 'profile.created';

    public const PROFILE_VERIFIED = 'profile.verified';

    public const PROFILE_BLOCKED = 'profile.blocked';

    public const PROFILE_DELETED = 'profile.deleted';
}
