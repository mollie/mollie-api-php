<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Capability;
use Mollie\Api\Resources\CapabilityCollection;

class CapabilityEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "capabilities";

    protected function getResourceCollectionObject($count, $_links)
    {
        return new CapabilityCollection($this->client, $count, $_links);
    }

    protected function getResourceObject()
    {
        return new Capability($this->client);
    }

    /**
     * Retrieve a single capability from Mollie.
     *
     * @param string $capabilityId
     * @param array $parameters
     * @return \Mollie\Api\Resources\Capability|\Mollie\Api\Resources\BaseResource
     * @throws ApiException
     */
    public function get(string $capabilityId)
    {
        return parent::rest_read($capabilityId, []);
    }
}
