<?php

declare(strict_types=1);

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
     * Payment events
     */
    public const PAYMENT_PAID = 'payment.paid';
    public const PAYMENT_AUTHORIZED = 'payment.authorized';
    public const PAYMENT_FAILED = 'payment.failed';
    public const PAYMENT_CANCELED = 'payment.canceled';
    public const PAYMENT_EXPIRED = 'payment.expired';
    public const PAYMENT_PENDING = 'payment.pending';

    /**
     * Refund events
     */
    public const REFUND_QUEUED = 'refund.queued';
    public const REFUND_PENDING = 'refund.pending';
    public const REFUND_PROCESSING = 'refund.processing';
    public const REFUND_REFUNDED = 'refund.refunded';
    public const REFUND_FAILED = 'refund.failed';
    public const REFUND_CANCELED = 'refund.canceled';

    /**
     * Chargeback events
     */
    public const CHARGEBACK_RECEIVED = 'chargeback.received';
    public const CHARGEBACK_REVERSED = 'chargeback.reversed';

    /**
     * Capture events
     */
    public const CAPTURE_SUCCEEDED = 'capture.succeeded';
    public const CAPTURE_FAILED = 'capture.failed';

    /**
     * Sales invoice e-invoice events
     */
    public const SALES_INVOICE_E_INVOICE_ISSUED = 'sales-invoice.e-invoice-issued';
    public const SALES_INVOICE_E_INVOICE_FAILED = 'sales-invoice.e-invoice-failed';

    /**
     * Business account draft transfer events
     */
    public const BUSINESS_ACCOUNT_DRAFT_TRANSFER_CREATED = 'business-account-draft-transfer.created';
    public const BUSINESS_ACCOUNT_DRAFT_TRANSFER_APPROVED = 'business-account-draft-transfer.approved';
    public const BUSINESS_ACCOUNT_DRAFT_TRANSFER_DECLINED = 'business-account-draft-transfer.declined';

    /**
     * Business account events
     */
    public const BUSINESS_ACCOUNT_CREATED = 'business-account.created';

    /**
     * Unmatched credit transfer action events
     */
    public const UNMATCHED_CREDIT_TRANSFER_MATCH_COMPLETED = 'unmatched-credit-transfer.match.completed';
    public const UNMATCHED_CREDIT_TRANSFER_MATCH_FAILED = 'unmatched-credit-transfer.match.failed';
    public const UNMATCHED_CREDIT_TRANSFER_RETURN_COMPLETED = 'unmatched-credit-transfer.return.completed';
    public const UNMATCHED_CREDIT_TRANSFER_RETURN_FAILED = 'unmatched-credit-transfer.return.failed';

    /**
     * Report events
     */
    public const REPORT_COMPLETED = 'report.completed';
    public const REPORT_FAILED = 'report.failed';

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
            self::PAYMENT_PAID,
            self::PAYMENT_AUTHORIZED,
            self::PAYMENT_FAILED,
            self::PAYMENT_CANCELED,
            self::PAYMENT_EXPIRED,
            self::PAYMENT_PENDING,
            self::REFUND_QUEUED,
            self::REFUND_PENDING,
            self::REFUND_PROCESSING,
            self::REFUND_REFUNDED,
            self::REFUND_FAILED,
            self::REFUND_CANCELED,
            self::CHARGEBACK_RECEIVED,
            self::CHARGEBACK_REVERSED,
            self::CAPTURE_SUCCEEDED,
            self::CAPTURE_FAILED,
            self::SALES_INVOICE_E_INVOICE_ISSUED,
            self::SALES_INVOICE_E_INVOICE_FAILED,
            self::BUSINESS_ACCOUNT_DRAFT_TRANSFER_CREATED,
            self::BUSINESS_ACCOUNT_DRAFT_TRANSFER_APPROVED,
            self::BUSINESS_ACCOUNT_DRAFT_TRANSFER_DECLINED,
            self::BUSINESS_ACCOUNT_CREATED,
            self::UNMATCHED_CREDIT_TRANSFER_MATCH_COMPLETED,
            self::UNMATCHED_CREDIT_TRANSFER_MATCH_FAILED,
            self::UNMATCHED_CREDIT_TRANSFER_RETURN_COMPLETED,
            self::UNMATCHED_CREDIT_TRANSFER_RETURN_FAILED,
            self::REPORT_COMPLETED,
            self::REPORT_FAILED,
            self::ALL,
        ];
    }
}
