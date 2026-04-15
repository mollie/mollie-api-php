<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

class ClientCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'clients';

    /**
     * Resource class name.
     */
    public static string $resource = Client::class;
}
