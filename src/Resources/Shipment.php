<?php

namespace Mollie\Api\Resources;

class Shipment extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * The shipment’s unique identifier,
     *
     * @example shp_3wmsgCJN4U
     * @var string
     */
    public $id;

    /**
     * Id of the order.
     *
     * @example ord_8wmqcHMN4U
     * @var string
     */
    public $orderId;

    /**
     * UTC datetime the shipment was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     * @var string|null
     */
    public $createdAt;

    /**
     * The order object lines contain the actual things the customer bought.
     * @var array|object[]
     */
    public $lines;

    /**
     * An object with several URL objects relevant to the customer. Every URL object will contain an href and a type field.
     * @var object[]
     */
    public $_links;

    /**
     * Get the line value objects
     *
     * @return OrderLineCollection
     */
    public function lines()
    {
        $lines  = new OrderLineCollection(count($this->lines), null);
        foreach ($this->lines as $line) {
            $lines->append(ResourceFactory::createFromApiResult($line, new OrderLine($this->client)));
        }

        return $lines;
    }

    /**
     * Get the Order object for this shipment
     *
     * @return Order
     */
    public function order()
    {
        return $this->client->orders->get($this->orderId);
    }
}
