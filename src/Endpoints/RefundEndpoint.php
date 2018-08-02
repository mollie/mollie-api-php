<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

class RefundEndpoint extends EndpointAbstract
{
    protected $resourcePath = "refunds";

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return Refund
     */
    protected function getResourceObject()
    {
        return new Refund($this->api);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return RefundCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new RefundCollection($this->api, $count, $_links);
    }

    /**
     * @param Payment $payment
     * @param string $refundId
     * @param array $parameters
     *
     * @return Refund
     */
    public function getFor(Payment $payment, $refundId, array $parameters = [])
    {
        $this->parentId = $payment->id;

        return parent::rest_read($refundId, $parameters);
    }

    /**
     * Retrieves a collection of Refunds from Mollie.
     *
     * @param string $from The first refund ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return RefundCollection
     * @throws ApiException
     */
    public function page($from = null, $limit = null, array $parameters = [])
    {
        return $this->rest_list($from, $limit, $parameters);
    }
}
