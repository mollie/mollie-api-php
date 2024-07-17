<?php

namespace Mollie\Api\Resources;

class CustomerCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "customers";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Customer::class;
}
