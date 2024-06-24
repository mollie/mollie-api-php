<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\Shipment;
use Mollie\Api\Resources\ShipmentCollection;

class ShipmentEndpoint extends EndpointCollection
{
    protected string $resourcePath = "orders_shipments";

    public const RESOURCE_ID_PREFIX = 'shp_';

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Shipment
    {
        return new Shipment($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): ShipmentCollection
    {
        return new ShipmentCollection($count, $_links);
    }

    /**
     * Create a shipment for some order lines. You can provide an empty array for the
     * "lines" option to include all unshipped lines for this order.
     *
     * @param Order $order
     * @param array $options
     * @param array $filters
     *
     * @return Shipment
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createFor(Order $order, array $options = [], array $filters = []): Shipment
    {
        return $this->createForId($order->id, $options, $filters);
    }

    /**
     * Create a shipment for some order lines. You can provide an empty array for the
     * "lines" option to include all unshipped lines for this order.
     *
     * @param string $orderId
     * @param array $options
     * @param array $filters
     *
     * @return Shipment
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createForId(string $orderId, array $options = [], array $filters = []): Shipment
    {
        $this->parentId = $orderId;

        return parent::createResource($options, $filters);
    }

    /**
     * Retrieve a single shipment and the order lines shipped by a shipment’s ID.
     *
     * @param Order $order
     * @param string $shipmentId
     * @param array $parameters
     *
     * @return Shipment
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getFor(Order $order, string $shipmentId, array $parameters = []): Shipment
    {
        return $this->getForId($order->id, $shipmentId, $parameters);
    }

    /**
     * Retrieve a single shipment and the order lines shipped by a shipment’s ID.
     *
     * @param string $orderId
     * @param string $shipmentId
     * @param array $parameters
     *
     * @return Shipment
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForId(string $orderId, string $shipmentId, array $parameters = []): Shipment
    {
        $this->parentId = $orderId;

        return parent::readResource($shipmentId, $parameters);
    }

    /**
     * Update a specific Order Shipment resource.
     *
     * Will throw an ApiException if the shipment id is invalid or the resource cannot be found.
     *
     * @param string $shipmentId
     * @param string $orderId
     * @param array $data
     *
     * @return Shipment
     * @throws ApiException
     */
    public function update(string $orderId, $shipmentId, array $data = []): Shipment
    {
        if (empty($shipmentId) || strpos($shipmentId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid subscription ID: '{$shipmentId}'. An subscription ID should start with '" . self::RESOURCE_ID_PREFIX . "'.");
        }

        $this->parentId = $orderId;

        return parent::updateResource($shipmentId, $data);
    }

    /**
     * Return all shipments for the Order provided.
     *
     * @param Order $order
     * @param array $parameters
     *
     * @return ShipmentCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listFor(Order $order, array $parameters = []): ShipmentCollection
    {
        return $this->listForId($order->id, $parameters);
    }

    /**
     * Return all shipments for the provided Order id.
     *
     * @param string $orderId
     * @param array $parameters
     *
     * @return ShipmentCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForId(string $orderId, array $parameters = []): ShipmentCollection
    {
        $this->parentId = $orderId;

        return parent::fetchCollection(null, null, $parameters);
    }
}
