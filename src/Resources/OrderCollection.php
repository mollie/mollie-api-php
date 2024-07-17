<?php

namespace Mollie\Api\Resources;

class OrderCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "orders";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Order::class;
}
