<?php

namespace Mollie\Api\Resources;

class RefundCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName()
    {
        return "refunds";
    }

    /**
     * Return the resource object
     *
     * @return BaseResource
     */
    protected function getResourceObject()
    {
        return new Refund($this->client);
    }
}
