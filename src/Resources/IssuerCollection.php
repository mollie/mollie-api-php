<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @extends ResourceCollection<\Mollie\Api\Resources\Issuer>
 */
class IssuerCollection extends ResourceCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'issuers';

    /**
     * Resource class name.
     */
    public static string $resource = Issuer::class;
}
