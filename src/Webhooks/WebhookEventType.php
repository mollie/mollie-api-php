<?php

namespace Mollie\Api\Webhooks;

class WebhookEventType
{
    /**
     * Payment Link events
     */
    public const PAYMENT_LINK_PAID = 'payment-link.paid';

    /**
     * Balance events
     */
    public const BALANCE_TRANSACTION_CREATED = 'balance-transaction.created';

    /**
     * Sales invoice events
     */
    public const SALES_INVOICE_CREATED = 'sales-invoice.created';
    public const SALES_INVOICE_ISSUED = 'sales-invoice.issued';
    public const SALES_INVOICE_CANCELED = 'sales-invoice.canceled';
    public const SALES_INVOICE_PAID = 'sales-invoice.paid';


    /**
     * Profile event types
     */
    public const PROFILE_CREATED = 'profile.created';
    public const PROFILE_VERIFIED = 'profile.verified';
    public const PROFILE_BLOCKED = 'profile.blocked';
    public const PROFILE_DELETED = 'profile.deleted';

    /**
     * Wildcard for all events
     */
    public const ALL = '*';

    /**
     * Get all available webhook event types.
     *
     * @return array<int, string>
     */
    public static function getAll(): array
    {
        return [
            self::PAYMENT_LINK_PAID,
            self::BALANCE_TRANSACTION_CREATED,
            self::SALES_INVOICE_CREATED,
            self::SALES_INVOICE_ISSUED,
            self::SALES_INVOICE_CANCELED,
            self::SALES_INVOICE_PAID,
            self::PROFILE_CREATED,
            self::PROFILE_VERIFIED,
            self::PROFILE_BLOCKED,
            self::PROFILE_DELETED,
            self::ALL,
        ];
    }
}
