<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\OrderLine;
use Mollie\Api\Resources\OrderLineCollection;
use Mollie\Api\Resources\ResourceFactory;

class OrderLineEndpoint extends EndpointCollection
{
    protected string $resourcePath = "orders_lines";

    public const RESOURCE_ID_PREFIX = 'odl_';

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): OrderLine
    {
        return new OrderLine($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): OrderLineCollection
    {
        return new OrderLineCollection($count, $_links);
    }

    /**
     * Update a specific OrderLine resource.
     *
     * Will throw an ApiException if the order line id is invalid or the resource cannot be found.
     *
     * @param string|null $orderId
     * @param string $orderlineId
     * @param array $data
     *
     * @return OrderLine
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update(string $orderId, string $orderlineId, array $data = []): OrderLine
    {
        $this->parentId = $orderId;

        if (empty($orderlineId) || strpos($orderlineId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid order line ID: '{$orderlineId}'. An order line ID should start with '" . self::RESOURCE_ID_PREFIX . "'.");
        }

        return parent::updateResource($orderlineId, $data);
    }

    /**
     * @param string $orderId
     * @param array $operations
     * @param array $parameters
     *
     * @return Order
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function updateMultiple(string $orderId, array $operations, array $parameters = []): Order
    {
        if (empty($orderId)) {
            throw new ApiException("Invalid resource id.");
        }

        $this->parentId = $orderId;

        $parameters['operations'] = $operations;

        $result = $this->client->performHttpCall(
            self::REST_UPDATE,
            "{$this->getResourcePath()}",
            $this->parseRequestBody($parameters)
        );

        return ResourceFactory::createFromApiResult($result, new Order($this->client));
    }

    /**
     * Cancel lines for the provided order.
     * The data array must contain a lines array.
     * You can pass an empty lines array if you want to cancel all eligible lines.
     *
     * @param Order $order
     * @param array $data
     *
     * @return void
     * @throws ApiException
     */
    public function cancelFor(Order $order, array $data): void
    {
        $this->cancelForId($order->id, $data);
    }

    /**
     * Cancel lines for the provided order id.
     * The data array must contain a lines array.
     * You can pass an empty lines array if you want to cancel all eligible lines.
     *
     * @param string $orderId
     * @param array $data
     *
     * @return void
     * @throws ApiException
     */
    public function cancelForId(string $orderId, array $data): void
    {
        if (!isset($data['lines']) || !is_array($data['lines'])) {
            throw new ApiException("A lines array is required.");
        }
        $this->parentId = $orderId;

        $this->client->performHttpCall(
            self::REST_DELETE,
            "{$this->getResourcePath()}",
            $this->parseRequestBody($data)
        );
    }
}
