<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @extends CursorCollection<\Mollie\Api\Resources\Payment>
 */
class PaymentCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'payments';

    /**
     * Resource class name.
     */
    public static string $resource = Payment::class;
}
