<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

/**
 * @method Refund[]|RefundCollection page($from = null, $limit = 50, array $filters = [])
 * @method Refund get($refundId, array $parameters = [])
 * @method Refund create(array $data = [], array $filters = [])
 * @method Refund delete($refundId)
 */
class PaymentRefundEndpoint extends EndpointAbstract
{
    /**
     * @var string
     */
    protected $resourcePath = "payments_refunds";

    /**
     * @return Refund
     */
    protected function getResourceObject()
    {
        return new Refund();
    }

    /**
     * Cancel the given Refund. This is just an alias of the 'delete' method.
     *
     * @param string $refundId
     *
     * @return Refund
     * @throws ApiException
     */
    public function cancel($refundId)
    {
        return $this->delete($refundId);
    }

    /**
     * Get the collection object that is used by this API. Every API uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return BaseCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new RefundCollection($count, $_links);
    }
}
