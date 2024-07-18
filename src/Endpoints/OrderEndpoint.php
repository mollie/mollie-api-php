<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\OrderCollection;

class OrderEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "orders";

    /**
     * Resource id prefix.
     * Used to validate resource id's.
     *
     * @var string
     */
    protected static string $resourceIdPrefix = 'ord_';

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Order::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = OrderCollection::class;

    /**
     * Creates a order in Mollie.
     *
     * @param array $data An array containing details on the order.
     * @param array $filters
     *
     * @return Order
     * @throws ApiException
     */
    public function create(array $data = [], array $filters = []): Order
    {
        /** @var Order */
        return $this->createResource($data, $filters);
    }

    /**
     * Update a specific Order resource
     *
     * Will throw a ApiException if the order id is invalid or the resource cannot be found.
     *
     * @param string $orderId
     * @param array $data
     *
     * @return null|Order
     * @throws ApiException
     */
    public function update(string $orderId, array $data = []): ?Order
    {
        $this->guardAgainstInvalidId($orderId);

        /** @var null|Order */
        return $this->updateResource($orderId, $data);
    }

    /**
     * Retrieve a single order from Mollie.
     *
     * Will throw a ApiException if the order id is invalid or the resource cannot
     * be found.
     *
     * @param string $orderId
     * @param array $parameters
     * @return Order
     * @throws ApiException
     */
    public function get(string $orderId, array $parameters = []): Order
    {
        $this->guardAgainstInvalidId($orderId);

        /** @var Order */
        return $this->readResource($orderId, $parameters);
    }

    /**
     * Cancel the given Order.
     *
     * If the order was partially shipped, the status will be "completed" instead of
     * "canceled".
     * Will throw a ApiException if the order id is invalid or the resource cannot
     * be found.
     * Returns the canceled order with HTTP status 200.
     *
     * @param string $orderId
     * @param array $parameters
     *
     * @return null|Order
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancel(string $orderId, $parameters = []): ?Order
    {
        /** @var null|Order */
        return $this->deleteResource($orderId, $parameters);
    }

    /**
     * Retrieves a collection of Orders from Mollie.
     *
     * @param string $from The first order ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return OrderCollection
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $parameters = []): OrderCollection
    {
        /** @var OrderCollection */
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over orders retrieved from Mollie.
     *
     * @param string $from The first order ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iterator(?string $from = null, ?int $limit = null, array $parameters = [], bool $iterateBackwards = false): LazyCollection
    {
        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
