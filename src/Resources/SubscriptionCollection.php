<?php

namespace Mollie\Api\Resources;

class SubscriptionCollection extends CursorCollection
{

    /**
     * @return string
     */
    public function getCollectionResourceName()
    {
        return "subscriptions";
    }

    /**
     * Return the resource object
     *
     * @return BaseResource
     */
    protected function getResourceObject()
    {
        return new Subscription($this->client);
    }
}