<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Settlement;
use Mollie\Api\Resources\SettlementCollection;

class SettlementsEndpoint extends EndpointAbstract
{
    protected $resourcePath = "settlements";

    /**
     * Get the object that is used by this API. Every API uses one type of object.
     *
     * @return \Mollie\Api\Resources\BaseResource
     */
    protected function getResourceObject()
    {
        return new Settlement($this->api);
    }

    /**
     * Get the collection object that is used by this API. Every API uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return \Mollie\Api\Resources\BaseCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new SettlementCollection($this->api, $count, $_links);
    }
}