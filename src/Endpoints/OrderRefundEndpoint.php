<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

class OrderRefundEndpoint extends EndpointCollection
{
    protected string $resourcePath = "orders_refunds";

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Refund
    {
        return new Refund($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): RefundCollection
    {
        return new RefundCollection($this->client, $count, $_links);
    }

    /**
     * Refund some order lines. You can provide an empty array for the
     * "lines" data to refund all eligible lines for this order.
     *
     * @param Order $order
     * @param array $data
     * @param array $filters
     *
     * @return Refund
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createFor(Order $order, array $data, array $filters = []): Refund
    {
        return $this->createForId($order->id, $data, $filters);
    }

    /**
     * Refund some order lines. You can provide an empty array for the
     * "lines" data to refund all eligible lines for this order.
     *
     * @param string $orderId
     * @param array $data
     * @param array $filters
     *
     * @return \Mollie\Api\Resources\Refund
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createForId(string $orderId, array $data, array $filters = []): Refund
    {
        $this->parentId = $orderId;

        return parent::rest_create($data, $filters);
    }
}
