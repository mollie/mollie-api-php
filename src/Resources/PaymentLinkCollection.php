<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @extends CursorCollection<\Mollie\Api\Resources\PaymentLink>
 */
class PaymentLinkCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'payment_links';

    /**
     * Resource class name.
     */
    public static string $resource = PaymentLink::class;
}
