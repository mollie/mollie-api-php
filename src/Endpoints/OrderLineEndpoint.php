<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Order;

class OrderLineEndpoint extends EndpointAbstract
{
    protected $resourcePath = "orders_lines";

    /**
     * @var string
     */
    const RESOURCE_ID_PREFIX = 'odl_';

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return OrderLine
     */
    protected function getResourceObject()
    {
        return new OrderLine($this->api);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
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
     * @param Order $order
     * @param string $subscriptionId
     *
     * @return null
     */
    public function cancelFor(Order $order, $orderId)
    {
        $this->parentId = $order->id;

        return parent::rest_delete($orderId);
    }
}
