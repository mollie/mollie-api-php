<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @extends ResourceCollection<\Mollie\Api\Resources\Capability>
 */
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
