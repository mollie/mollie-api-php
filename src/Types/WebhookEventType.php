<?php

namespace Mollie\Api\Types;

class WebhookEventType
{
    /**
     * A payment link moved to the paid status.
     */
    public const PAYMENT_LINK_PAID = 'payment-link.paid';

    /**
     * Get all available webhook event types.
     *
     * @return array
     */
    public static function getAll(): array
    {
        return [
            self::PAYMENT_LINK_PAID,
        ];
    }
}
