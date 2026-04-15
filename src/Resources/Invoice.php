<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Types\InvoiceStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Invoice extends BaseResource
{
    public string $id;

    public ?string $reference = null;

    public ?string $vatNumber = null;

    public InvoiceStatus|string|null $status = null;

    /**
     * Date the invoice was issued, e.g. 2018-01-01.
     */
    public ?string $issuedAt = null;

    /**
     * Date the invoice was paid, e.g. 2018-01-01.
     */
    public ?string $paidAt = null;

    /**
     * Date the invoice is due, e.g. 2018-01-01.
     */
    public ?string $dueAt = null;

    /**
     * Total amount of the invoice excluding VAT.
     */
    public ?Money $netAmount = null;

    /**
     * VAT amount of the invoice.
     */
    public ?Money $vatAmount = null;

    /**
     * Total amount of the invoice including VAT.
     */
    public ?Money $grossAmount = null;

    /**
     * Array containing the invoice lines.
     *
     * @var array|null
     */
    public ?array $lines = null;

    /**
     * Contains a PDF link to the invoice.
     *
     * @var \stdClass|null
     */
    public $_links;

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::Paid;
    }

    public function isOpen(): bool
    {
        return $this->status === InvoiceStatus::Open;
    }

    public function isOverdue(): bool
    {
        return $this->status === InvoiceStatus::Overdue;
    }
}
