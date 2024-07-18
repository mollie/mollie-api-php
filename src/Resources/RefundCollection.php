<?php

namespace Mollie\Api\Resources;

class RefundCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "refunds";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Refund::class;
}
