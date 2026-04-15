<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @extends ResourceCollection<\Mollie\Api\Resources\Route>
 */
class RouteCollection extends ResourceCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'routes';

    /**
     * Resource class name.
     */
    public static string $resource = Route::class;
}
