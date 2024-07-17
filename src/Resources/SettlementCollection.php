<?php

namespace Mollie\Api\Resources;

class SettlementCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "settlements";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Settlement::class;
}
