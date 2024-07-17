<?php

namespace Mollie\Api\Resources;

class BalanceCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "balances";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Balance::class;
}
