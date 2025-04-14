<?php

namespace Mollie\Api\Resources;

class ChargebackCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'chargebacks';

    /**
     * Resource class name.
     */
    public static string $resource = Chargeback::class;
}
