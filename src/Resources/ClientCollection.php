<?php

namespace Mollie\Api\Resources;

class ClientCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "clients";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Client::class;
}
