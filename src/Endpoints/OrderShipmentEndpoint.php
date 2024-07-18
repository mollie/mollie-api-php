<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\Shipment;
use Mollie\Api\Resources\ShipmentCollection;

class OrderShipmentEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "orders_shipments";

    /**
     * Resource id prefix.
     * Used to validate resource id's.
     *
     * @var string
     */
    protected static string $resourceIdPrefix = 'shp_';

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Shipment::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = ShipmentCollection::class;

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

        /** @var Shipment */
        return $this->createResource($options, $filters);
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

        /** @var Shipment */
        return $this->readResource($shipmentId, $parameters);
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
     * @return null|Shipment
     * @throws ApiException
     */
    public function update(string $orderId, $shipmentId, array $data = []): ?Shipment
    {
        $this->guardAgainstInvalidId($shipmentId);

        $this->parentId = $orderId;

        /** @var null|Shipment */
        return $this->updateResource($shipmentId, $data);
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

        /** @var ShipmentCollection */
        return $this->fetchCollection(null, null, $parameters);
    }
}
