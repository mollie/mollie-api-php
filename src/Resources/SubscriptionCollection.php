<?php

namespace Mollie\Api\Resources;

class SubscriptionCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'subscriptions';

    /**
     * Resource class name.
     */
    public static string $resource = Subscription::class;
}
