<?php

namespace Mollie\Api\Resources;

class CustomerCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName()
    {
        return "customers";
    }

    /**
     * Return the resource object
     *
     * @return BaseResource
     */
    protected function getResourceObject()
    {
        return new Customer($this->client);
    }
}