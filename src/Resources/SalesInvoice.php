<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Types\SalesInvoiceStatus;

class SalesInvoice extends BaseResource
{

    public string $id;

    public ?string $profileId = null;

    public ?string $invoiceNumber = null;

    public string $currency;

    public SalesInvoiceStatus|string $status;

    public string $vatScheme;

    public string $vatMode;

    public ?string $memo = null;

    public string $paymentTerm;

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

    /** @var array<mixed> */
    public array $lines;

    public string $webhookUrl;

    /**
     * @var \stdClass|null
     */
    public $discount;

    public bool $isEInvoice;

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
        return $this->status === SalesInvoiceStatus::Draft;
    }

    public function isIssued(): bool
    {
        return $this->status === SalesInvoiceStatus::Issued;
    }

    public function isPaid(): bool
    {
        return $this->status === SalesInvoiceStatus::Paid;
    }
}
