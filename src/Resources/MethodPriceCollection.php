<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @extends ResourceCollection<\Mollie\Api\Resources\MethodPrice>
 */
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
