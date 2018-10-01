<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Order;

class OrderLineEndpoint extends EndpointAbstract
{
    protected $resourcePath = "orders_lines";

    /**
     * @var string
     */
    const RESOURCE_ID_PREFIX = 'odl_';

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one
     * type of object.
     *
     * @return OrderLine
     */
    protected function getResourceObject()
    {
        return new OrderLine($this->api);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API
     * endpoint uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return OrderLineCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new OrderLineCollection($this->api, $count, $_links);
    }

    /**
     * Cancel a line for the provided order.
     * Returns HTTP status 204 (no content) if succesful.
     *
     * @param Order $order
     * @param string $lineId
     * @param array $data
     *
     * @return null
     */
    public function cancelFor(Order $order, $lineId, $data = [])
    {
        $this->parentId = $order->id;

        return parent::rest_delete($lineId, $data);
    }
}
