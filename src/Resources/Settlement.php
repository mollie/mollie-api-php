<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Types\SettlementStatus;

class Settlement extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

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
     * @var string
     */
    public $createdAt;

    /**
     * Status of the settlement.
     *
     * @var string
     */
    public $status;

    /**
     * Total settlement amount in euros.
     *
     * @var object
     */
    public $amount;

    /**
     * Revenues and costs nested per year, per month, and per payment method.
     *
     * @var object
     */
    public $periods;

    /**
     * @var object[]
     */
    public $_links;

    /**
     * Is this settlement still open?
     *
     * @return bool
     */
    public function isOpen()
    {
        return $this->status === SettlementStatus::STATUS_OPEN;
    }

    /**
     * Is this settlement pending?
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === SettlementStatus::STATUS_PENDING;
    }

    /**
     * Is this settlement paidout?
     *
     * @return bool
     */
    public function isPaidout()
    {
        return $this->status === SettlementStatus::STATUS_PAIDOUT;
    }

    /**
     * Is this settlement failed?
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === SettlementStatus::STATUS_FAILED;
    }
}