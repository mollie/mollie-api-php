<?php

namespace Mollie\Api\Resources;

class MethodCollection extends ResourceCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'methods';

    public static string $resource = Method::class;
}
