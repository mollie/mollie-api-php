<?php

namespace Mollie\Api\Resources;

class OrderCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "orders";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Order::class;
    }
}
