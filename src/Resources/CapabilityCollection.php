<?php

namespace Mollie\Api\Resources;

class CapabilityCollection extends ResourceCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'capabilities';

    /**
     * Resource class name.
     */
    public static string $resource = Capability::class;
}
