<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @extends CursorCollection<\Mollie\Api\Resources\Chargeback>
 */
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
