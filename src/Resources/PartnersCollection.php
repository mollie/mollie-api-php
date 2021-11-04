<?php

namespace Mollie\Api\Resources;

class PartnersCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName()
    {
        return "clients";
    }

    /**
     * @return BaseResource
     */
    protected function createResourceObject()
    {
        return new Partners($this->client);
    }
}
