<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

class OrderRefundEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "orders_refunds";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Refund::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = RefundCollection::class;

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
     * @return Refund
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createForId(string $orderId, array $data, array $filters = []): Refund
    {
        $this->parentId = $orderId;

        /** @var Refund */
        return $this->createResource($data, $filters);
    }

    /**
     * @param $orderId
     * @param array $parameters
     * @return RefundCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pageForId($orderId, array $parameters = []): RefundCollection
    {
        $this->parentId = $orderId;

        /** @var RefundCollection */
        return $this->fetchCollection(null, null, $parameters);
    }

    /**
     * @param Order $order
     * @param array $parameters
     * @return RefundCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pageFor(Order $order, array $parameters = []): RefundCollection
    {
        return $this->pageForId($order->id, $parameters);
    }
}
