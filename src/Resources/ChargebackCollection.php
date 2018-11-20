<?php

namespace Mollie\Api\Resources;

class ChargebackCollection extends BaseCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName()
    {
        return "chargebacks";
    }

    /**
     * @return BaseResource
     */
    protected function createResourceObject()
    {
        return new Chargeback($this->client);
    }
}