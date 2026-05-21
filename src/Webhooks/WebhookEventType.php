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
     * Payout events
     */
    public const PAYOUT_INITIATED = 'payout.initiated';
    public const PAYOUT_PROCESSING_AT_BANK = 'payout.processing-at-bank';
    public const PAYOUT_COMPLETED = 'payout.completed';
    public const PAYOUT_CANCELED = 'payout.canceled';
    public const PAYOUT_FAILED = 'payout.failed';

    /**
     * Connect balance transfer events
     */
    public const CONNECT_BALANCE_TRANSFER_FAILED = 'connect-balance-transfer.failed';
    public const CONNECT_BALANCE_TRANSFER_SUCCEEDED = 'connect-balance-transfer.succeeded';

    /**
     * Dispute events
     */
    public const DISPUTE_CREATED = 'dispute.created';
    public const DISPUTE_RESOLVED = 'dispute.resolved';
    public const DISPUTE_UPDATED = 'dispute.updated';

    /**
     * File events
     */
    public const FILE_ACCEPTED = 'file.accepted';
    public const FILE_REJECTED = 'file.rejected';
    public const FILE_FAILED = 'file.failed';

    /**
     * Unmatched credit transfer events
     */
    public const UNMATCHED_CREDIT_TRANSFER_RECEIVED = 'unmatched-credit-transfer.received';
    public const UNMATCHED_CREDIT_TRANSFER_MATCHED = 'unmatched-credit-transfer.matched';
    public const UNMATCHED_CREDIT_TRANSFER_RETURNED = 'unmatched-credit-transfer.returned';
    public const UNMATCHED_CREDIT_TRANSFER_EXPIRED = 'unmatched-credit-transfer.expired';

    /**
     * Business account transfer events
     */
    public const BUSINESS_ACCOUNT_TRANSFER_REQUESTED = 'business-account-transfer.requested';
    public const BUSINESS_ACCOUNT_TRANSFER_INITIATED = 'business-account-transfer.initiated';
    public const BUSINESS_ACCOUNT_TRANSFER_PENDING_REVIEW = 'business-account-transfer.pending-review';
    public const BUSINESS_ACCOUNT_TRANSFER_PROCESSED = 'business-account-transfer.processed';
    public const BUSINESS_ACCOUNT_TRANSFER_FAILED = 'business-account-transfer.failed';
    public const BUSINESS_ACCOUNT_TRANSFER_BLOCKED = 'business-account-transfer.blocked';
    public const BUSINESS_ACCOUNT_TRANSFER_RETURNED = 'business-account-transfer.returned';

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
            self::PAYOUT_INITIATED,
            self::PAYOUT_PROCESSING_AT_BANK,
            self::PAYOUT_COMPLETED,
            self::PAYOUT_CANCELED,
            self::PAYOUT_FAILED,
            self::CONNECT_BALANCE_TRANSFER_FAILED,
            self::CONNECT_BALANCE_TRANSFER_SUCCEEDED,
            self::DISPUTE_CREATED,
            self::DISPUTE_RESOLVED,
            self::DISPUTE_UPDATED,
            self::FILE_ACCEPTED,
            self::FILE_REJECTED,
            self::FILE_FAILED,
            self::UNMATCHED_CREDIT_TRANSFER_RECEIVED,
            self::UNMATCHED_CREDIT_TRANSFER_MATCHED,
            self::UNMATCHED_CREDIT_TRANSFER_RETURNED,
            self::UNMATCHED_CREDIT_TRANSFER_EXPIRED,
            self::BUSINESS_ACCOUNT_TRANSFER_REQUESTED,
            self::BUSINESS_ACCOUNT_TRANSFER_INITIATED,
            self::BUSINESS_ACCOUNT_TRANSFER_PENDING_REVIEW,
            self::BUSINESS_ACCOUNT_TRANSFER_PROCESSED,
            self::BUSINESS_ACCOUNT_TRANSFER_FAILED,
            self::BUSINESS_ACCOUNT_TRANSFER_BLOCKED,
            self::BUSINESS_ACCOUNT_TRANSFER_RETURNED,
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
