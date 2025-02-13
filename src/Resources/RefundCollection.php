<?php

namespace Mollie\Api\Resources;

class RefundCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'refunds';

    /**
     * Resource class name.
     */
    public static string $resource = Refund::class;
}
