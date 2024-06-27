<?php

namespace Mollie\Api\Resources;

class SubscriptionCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "subscriptions";
    }

    /**
     * @return Subscription
     */
    protected function createResourceObject(): Subscription
    {
        return new Subscription($this->client);
    }
}
