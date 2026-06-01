<?php

namespace Mollie\Api\Resources;

class PayoutCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'payouts';

    /**
     * Resource class name.
     */
    public static string $resource = Payout::class;
}
