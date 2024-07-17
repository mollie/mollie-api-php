<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;

class OrderPaymentEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "orders_payments";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Payment::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = PaymentCollection::class;

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

        /** @var Payment */
        return $this->createResource($data, $filters);
    }
}
