<?php

namespace Mollie\Api\Resources;

class CapabilityCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName()
    {
        return "capabilities";
    }

    /**
     * @return BaseResource
     */
    protected function createResourceObject()
    {
        return new Capability($this->client);
    }
}
