<?php

namespace Mollie\Api\Resources;

class MethodPriceCollection extends ResourceCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'prices';

    /**
     * Resource class name.
     */
    public static string $resource = MethodPrice::class;
}
