<?php

namespace Mollie\Api\Resources;

class SettlementCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'settlements';

    /**
     * Resource class name.
     */
    public static string $resource = Settlement::class;
}
