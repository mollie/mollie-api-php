<?php

namespace Mollie\Api\Resources;

class CustomerCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "customers";
    }

    /**
     * @return Customer
     */
    protected function createResourceObject(): Customer
    {
        return new Customer($this->client);
    }
}
