<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Types\SettlementStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Settlement extends BaseResource
{
    public string $id;

    /**
     * The settlement reference. This corresponds to an invoice that's in your Dashboard.
     */
    public ?string $reference = null;

    /**
     * UTC datetime the settlement was created in ISO-8601 format.
     */
    public ?string $createdAt = null;

    /**
     * The date on which the settlement was settled.
     */
    public ?string $settledAt = null;

    public SettlementStatus|string|null $status = null;

    /**
     * Total settlement amount.
     */
    public ?Money $amount = null;

    /**
     * Revenues and costs nested per year, per month, and per payment method.
     *
     * @var \stdClass|null
     */
    public $periods;

    /**
     * The ID of the invoice on which this settlement is invoiced, if it has been invoiced.
     */
    public ?string $invoiceId = null;

    /**
     * @var \stdClass|null
     */
    public $_links;

    public function isOpen(): bool
    {
        return $this->status === SettlementStatus::Open;
    }

    public function isPending(): bool
    {
        return $this->status === SettlementStatus::Pending;
    }

    public function isPaidout(): bool
    {
        return $this->status === SettlementStatus::Paidout;
    }

    public function isFailed(): bool
    {
        return $this->status === SettlementStatus::Failed;
    }

    /**
     * Retrieve the first page of payments associated with this settlement.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function payments(?int $limit = null, array $parameters = []): PaymentCollection
    {
        return $this->connector->settlementPayments->pageForId(
            $this->id,
            array_merge($parameters, ['limit' => $limit])
        );
    }

    /**
     * Retrieve the first page of refunds associated with this settlement.
     *
     * @throws ApiException
     */
    public function refunds(?int $limit = null, array $parameters = []): RefundCollection
    {
        return $this->connector->settlementRefunds->pageForId(
            $this->id,
            array_merge($parameters, ['limit' => $limit])
        );
    }

    /**
     * Retrieve the first page of chargebacks associated with this settlement.
     *
     * @throws ApiException
     */
    public function chargebacks(?int $limit = null, array $parameters = []): ChargebackCollection
    {
        return $this->connector->settlementChargebacks->pageForId(
            $this->id,
            array_merge($parameters, ['limit' => $limit])
        );
    }

    /**
     * Retrieve the first page of cap associated with this settlement.
     *
     * @throws ApiException
     */
    public function captures(?int $limit = null, array $parameters = []): CaptureCollection
    {
        return $this->connector->settlementCaptures->pageForId(
            $this->id,
            array_merge($parameters, ['limit' => $limit])
        );
    }
}
