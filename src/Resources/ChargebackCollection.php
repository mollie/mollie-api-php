<?php

namespace Mollie\Api\Resources;

class ChargebackCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "chargebacks";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Chargeback::class;
}
