<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;

class OrderPaymentEndpoint extends EndpointAbstract
{
    protected $resourcePath = "orders_payments";

    /**
     * @var string
     */
    const RESOURCE_ID_PREFIX = 'tr_';

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one
     * type of object.
     *
     * @return \Mollie\Api\Resources\Payment
     */
    protected function getResourceObject()
    {
        return new Payment($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API
     * endpoint uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return \Mollie\Api\Resources\PaymentCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new PaymentCollection($this->client, $count, $_links);
    }

    /**
     * Creates an order payment in Mollie.
     *
     * @param \Mollie\Api\Resources\Order $order
     * @param array $data An array containing details on the order payment.
     * @param array $filters
     *
     * @return \Mollie\Api\Resources\BaseResource|\Mollie\Api\Resources\Order
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createFor(Order $order, array $data, array $filters = [])
    {
        $this->parentId = $order->id;

        return $this->rest_create($data, $filters);
    }
}
