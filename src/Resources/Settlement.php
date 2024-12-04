<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Types\SettlementStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Settlement extends BaseResource
{
    /**
     * Id of the settlement.
     *
     * @var string
     */
    public $id;

    /**
     * The settlement reference. This corresponds to an invoice that's in your Dashboard.
     *
     * @var string
     */
    public $reference;

    /**
     * UTC datetime the payment was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string
     */
    public $createdAt;

    /**
     * The date on which the settlement was settled, in ISO 8601 format. When requesting the open settlement or next settlement the return value is null.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $settledAt;

    /**
     * Status of the settlement.
     *
     * @var string
     */
    public $status;

    /**
     * Total settlement amount in euros.
     *
     * @var \stdClass
     */
    public $amount;

    /**
     * Revenues and costs nested per year, per month, and per payment method.
     *
     * @var \stdClass
     */
    public $periods;

    /**
     * The ID of the invoice on which this settlement is invoiced, if it has been invoiced.
     *
     * @var string|null
     */
    public $invoiceId;

    /**
     * @var \stdClass
     */
    public $_links;

    /**
     * Is this settlement still open?
     */
    public function isOpen(): bool
    {
        return $this->status === SettlementStatus::OPEN;
    }

    /**
     * Is this settlement pending?
     */
    public function isPending(): bool
    {
        return $this->status === SettlementStatus::PENDING;
    }

    /**
     * Is this settlement paid out?
     */
    public function isPaidout(): bool
    {
        return $this->status === SettlementStatus::PAIDOUT;
    }

    /**
     * Has this settlement failed?
     */
    public function isFailed(): bool
    {
        return $this->status === SettlementStatus::FAILED;
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
