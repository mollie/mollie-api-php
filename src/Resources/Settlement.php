<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
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

    /**
     * Retrieves all payments associated with this settlement
     *
     * @return PaymentCollection
     * @throws ApiException
     */
    public function payments()
    {
        if (!isset($this->_links->payments->href)) {
            return new PaymentCollection($this->client, 0, null);
        }

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->payments->href);

        $resourceCollection = new PaymentCollection($this->client, $result->count, $result->_links);
        foreach ($result->_embedded->payments as $dataResult) {
            $resourceCollection[] = ResourceFactory::createFromApiResult($dataResult, new Payment($this->client));
        }

        return $resourceCollection;
    }

    /**
     * Retrieves all refunds associated with this settlement
     *
     * @return RefundCollection
     * @throws ApiException
     */
    public function refunds()
    {
        if (!isset($this->_links->refunds->href)) {
            return new RefundCollection($this->client, 0, null);
        }

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->refunds->href);

        $resourceCollection = new RefundCollection($this->client, $result->count, $result->_links);
        foreach ($result->_embedded->refunds as $dataResult) {
            $resourceCollection[] = ResourceFactory::createFromApiResult($dataResult, new Refund($this->client));
        }

        return $resourceCollection;
    }

    /**
     * Retrieves all chargebacks associated with this settlement
     *
     * @return ChargebackCollection
     * @throws ApiException
     */
    public function chargebacks()
    {
        if (!isset($this->_links->chargebacks->href)) {
            return new ChargebackCollection($this->client, 0, null);
        }

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->chargebacks->href);

        $resourceCollection = new ChargebackCollection($this->client, $result->count, $result->_links);
        foreach ($result->_embedded->chargebacks as $dataResult) {
            $resourceCollection[] = ResourceFactory::createFromApiResult($dataResult, new Chargeback($this->client));
        }

        return $resourceCollection;
    }
}