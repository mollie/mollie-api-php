<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\RefundStatus;

class Refund extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * Id of the payment method.
     *
     * @var string
     */
    public $id;

    /**
     * The $amount that was refunded.
     *
     * @var object
     */
    public $amount;

    /**
     * UTC datetime the payment was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     * @var string
     */
    public $createdAt;

    /**
     * The refund's description, if available.
     *
     * @var string|null
     */
    public $description;

    /**
     * The payment id that was refunded.
     *
     * @var string
     */
    public $paymentId;

    /**
     * The settlement amount
     *
     * @var object
     */
    public $settlementAmount;

    /**
     * The refund status
     *
     * @var string
     */
    public $status;

    /**
     * @var object[]
     */
    public $_links;

    /**
     * Is this refund queued?
     *
     * @return bool
     */
    public function isQueued()
    {
        return $this->status === RefundStatus::STATUS_QUEUED;
    }

    /**
     * Is this refund pending?
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === RefundStatus::STATUS_PENDING;
    }

    /**
     * Is this refund processing?
     *
     * @return bool
     */
    public function isProcessing()
    {
        return $this->status === RefundStatus::STATUS_PROCESSING;
    }

    /**
     * Is this refund transferred to consumer?
     *
     * @return bool
     */
    public function isTransferred()
    {
        return $this->status === RefundStatus::STATUS_REFUNDED;
    }

    /**
     * Cancel the refund
     *
     * @return BaseResource
     */
    public function cancel()
    {
        $dataResult = $this->client->performHttpCallToFullUrl(
            MollieApiClient::HTTP_DELETE,
            $this->_links->self->href
        );

        return ResourceFactory::createFromApiResult($dataResult, new self($this->client));
    }
}
