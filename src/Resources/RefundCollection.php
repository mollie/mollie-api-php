<?php

namespace Mollie\Api\Resources;

class RefundCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "refunds";
    }

    /**
     * @return Refund
     */
    protected function createResourceObject(): Refund
    {
        return new Refund($this->client);
    }
}
