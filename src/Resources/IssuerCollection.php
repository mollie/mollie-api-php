<?php

namespace Mollie\Api\Resources;

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
