<?php

namespace Mollie\Api\Types;

class WebhookEventType
{
    /**
     * A payment link moved to the paid status.
     */
    public const PAYMENT_LINK_PAID = 'payment-link.paid';

    /**
     * A profile was created.
     */
    public const PROFILE_CREATED = 'profile.created';

    /**
     * A profile was verified.
     */
    public const PROFILE_VERIFIED = 'profile.verified';

    /**
     * A profile was blocked.
     */
    public const PROFILE_BLOCKED = 'profile.blocked';

    /**
     * A profile was deleted.
     */
    public const PROFILE_DELETED = 'profile.deleted';

    /**
     * Get all available webhook event types.
     */
    public static function getAll(): array
    {
        return [
            self::PAYMENT_LINK_PAID,
            self::PROFILE_CREATED,
            self::PROFILE_VERIFIED,
            self::PROFILE_BLOCKED,
            self::PROFILE_DELETED,
        ];
    }
}
