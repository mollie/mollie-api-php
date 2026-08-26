<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;
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
}
