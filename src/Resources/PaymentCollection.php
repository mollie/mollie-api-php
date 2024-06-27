<?php

namespace Mollie\Api\Resources;

class PaymentCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "payments";
    }

    /**
     * @return Payment
     */
    protected function createResourceObject(): Payment
    {
        return new Payment($this->client);
    }
}
