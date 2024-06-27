<?php

namespace Mollie\Api\Resources;

class PaymentLinkCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "payment_links";
    }

    /**
     * @return PaymentLink
     */
    protected function createResourceObject(): PaymentLink
    {
        return new PaymentLink($this->client);
    }
}
