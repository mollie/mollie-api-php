<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\OrderCollection;

class OrderEndpoint extends CollectionEndpointAbstract
{
    protected string $resourcePath = "orders";

    public const string RESOURCE_ID_PREFIX = 'ord_';

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Order
    {
        return new Order($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): OrderCollection
    {
        return new OrderCollection($this->client, $count, $_links);
    }

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
        return $this->rest_create($data, $filters);
    }

    /**
     * Update a specific Order resource
     *
     * Will throw a ApiException if the order id is invalid or the resource cannot be found.
     *
     * @param string $orderId
     * @param array $data
     *
     * @return Order
     * @throws ApiException
     */
    public function update(string $orderId, array $data = []): Order
    {
        if (empty($orderId) || strpos($orderId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid order ID: '{$orderId}'. An order ID should start with '" . self::RESOURCE_ID_PREFIX . "'.");
        }

        return parent::rest_update($orderId, $data);
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
        if (empty($orderId) || strpos($orderId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid order ID: '{$orderId}'. An order ID should start with '" . self::RESOURCE_ID_PREFIX . "'.");
        }

        return parent::rest_read($orderId, $parameters);
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
        return $this->rest_delete($orderId, $parameters);
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
        return $this->rest_list($from, $limit, $parameters);
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
        return $this->rest_iterator($from, $limit, $parameters, $iterateBackwards);
    }
}
