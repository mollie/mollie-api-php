<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\Shipment;
use Mollie\Api\Resources\ShipmentCollection;

class ShipmentEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "orders_shipments";

    /**
     * @var string
     */
    const RESOURCE_ID_PREFIX = 'shp_';

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return Shipment
     */
    protected function getResourceObject()
    {
        return new Shipment($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API
     * endpoint uses one type of collection object.
     *
     * @param int $count
     * @param \stdClass $_links
     *
     * @return ShipmentCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new ShipmentCollection($this->client, $count, $_links);
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
    public function createFor(Order $order, array $options = [], array $filters = [])
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
    public function createForId($orderId, array $options = [], array $filters = [])
    {
        $this->parentId = $orderId;

        return parent::rest_create($options, $filters);
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
    public function getFor(Order $order, $shipmentId, array $parameters = [])
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
     * @return \Mollie\Api\Resources\BaseResource|\Mollie\Api\Resources\Shipment
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForId($orderId, $shipmentId, array $parameters = [])
    {
        $this->parentId = $orderId;

        return parent::rest_read($shipmentId, $parameters);
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
    public function listFor(Order $order, array $parameters = [])
    {
        return $this->listForId($order->id, $parameters);
    }

    /**
     * Return all shipments for the provided Order id.
     *
     * @param string $orderId
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\BaseCollection|\Mollie\Api\Resources\ShipmentCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForId($orderId, array $parameters = [])
    {
        $this->parentId = $orderId;

        return parent::rest_list(null, null, $parameters);
    }
}
