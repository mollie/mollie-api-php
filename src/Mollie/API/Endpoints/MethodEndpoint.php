<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;

/**
 * @method Method[]|MethodCollection all($from = null, $limit = 50, array $filters = [])
 * @method Method get($id, array $filters = [])
 */
class MethodEndpoint extends EndpointAbstract
{
    protected $resourcePath = "methods";

    /**
     * @return Method
     */
    protected function getResourceObject()
    {
        return new Method();
    }

    /**
     * Get the collection object that is used by this API. Every API uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return BaseCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new MethodCollection($count, $_links);
    }
}
