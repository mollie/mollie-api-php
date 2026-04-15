<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @extends ResourceCollection<\Mollie\Api\Resources\Method>
 */
class MethodCollection extends ResourceCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'methods';

    public static string $resource = Method::class;
}
