<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Types\SalesInvoiceEInvoiceStatus;
use Mollie\Api\Types\SalesInvoiceStatus;
use Mollie\Api\Utils\Utility;

class SalesInvoice extends BaseResource
{

    public string $id;

    public ?string $profileId = null;

    public ?string $invoiceNumber = null;

    public ?string $currency = null;

    public SalesInvoiceStatus|string $status;

    public string $vatScheme;

    public string $vatMode;

    public ?string $memo = null;

    public ?string $paymentTerm = null;

    /**
     * @var \stdClass
     */
    public $paymentDetails;

    /**
     * @var \stdClass
     */
    public $emailDetails;

    public string $recipientIdentifier;

    /**
     * @var \stdClass
     */
    public $recipient;

    /** @var array<mixed>|null */
    public ?array $lines = null;

    public ?string $webhookUrl = null;

    /**
     * @var \stdClass|null
     */
    public $discount;

    public bool $isEInvoice;

    /**
     * The e-invoice submission status, when the invoice is an e-invoice.
     */
    public SalesInvoiceEInvoiceStatus|string|null $eInvoiceStatus = null;

    public Money $amountDue;

    public Money $subtotalAmount;

    public Money $totalAmount;

    public Money $totalVatAmount;

    public Money $discountedSubtotalAmount;

    public string $createdAt;

    public ?string $issuedAt = null;

    public ?string $dueAt = null;

    /**
     * @var \stdClass
     */
    public $_links;

    public function isDraft(): bool
    {
        return Utility::equals($this->status, SalesInvoiceStatus::Draft);
    }

    public function isIssued(): bool
    {
        return Utility::equals($this->status, SalesInvoiceStatus::Issued);
    }

    public function isPaid(): bool
    {
        return Utility::equals($this->status, SalesInvoiceStatus::Paid);
    }

    public function isIssuing(): bool
    {
        return Utility::equals($this->status, SalesInvoiceStatus::Issuing);
    }

    public function isPendingPayment(): bool
    {
        return Utility::equals($this->status, SalesInvoiceStatus::PendingPayment);
    }

    public function isOverdue(): bool
    {
        return Utility::equals($this->status, SalesInvoiceStatus::Overdue);
    }

    public function isPaymentReversed(): bool
    {
        return Utility::equals($this->status, SalesInvoiceStatus::PaymentReversed);
    }

    public function isCancelled(): bool
    {
        return Utility::equals($this->status, SalesInvoiceStatus::Cancelled);
    }

    public function isExpired(): bool
    {
        return Utility::equals($this->status, SalesInvoiceStatus::Expired);
    }

    public function isFailed(): bool
    {
        return Utility::equals($this->status, SalesInvoiceStatus::Failed);
    }
}
