<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

class PaymentRefundEndpoint extends EndpointAbstract
{
    protected $resourcePath = "payments_refunds";

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
}
