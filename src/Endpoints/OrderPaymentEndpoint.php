<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;

class OrderPaymentEndpoint extends CollectionEndpointAbstract
{
    protected string $resourcePath = "orders_payments";

    public const RESOURCE_ID_PREFIX = 'tr_';

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Payment
    {
        return new Payment($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): PaymentCollection
    {
        return new PaymentCollection($this->client, $count, $_links);
    }

    /**
     * Creates a payment in Mollie for a specific order.
     *
     * @param Order $order
     * @param array $data An array containing details on the order payment.
     * @param array $filters
     *
     * @return Payment
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createFor(Order $order, array $data, array $filters = []): Payment
    {
        return $this->createForId($order->id, $data, $filters);
    }

    /**
     * Creates a payment in Mollie for a specific order ID.
     *
     * @param string $orderId
     * @param array $data An array containing details on the order payment.
     * @param array $filters
     *
     * @return Payment
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createForId(string $orderId, array $data, array $filters = []): Payment
    {
        $this->parentId = $orderId;

        return $this->rest_create($data, $filters);
    }
}
