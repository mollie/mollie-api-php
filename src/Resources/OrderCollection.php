<?php

namespace Mollie\Api\Resources;

class OrderCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "orders";
    }

    /**
     * @return Order
     */
    protected function createResourceObject(): Order
    {
        return new Order($this->client);
    }
}
