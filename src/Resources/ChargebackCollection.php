<?php

namespace Mollie\Api\Resources;

class ChargebackCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "chargebacks";
    }

    /**
     * @return Chargeback
     */
    protected function createResourceObject(): Chargeback
    {
        return new Chargeback($this->client);
    }
}
