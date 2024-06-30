<?php

namespace Mollie\Api\Resources;

class SubscriptionCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "subscriptions";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Subscription::class;
    }
}
