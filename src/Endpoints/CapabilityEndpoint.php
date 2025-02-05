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
}
