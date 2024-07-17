<?php

namespace Mollie\Api\Resources;

class SubscriptionCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "subscriptions";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Subscription::class;
}
