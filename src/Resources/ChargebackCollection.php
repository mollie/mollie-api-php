<?php

namespace Mollie\Api\Resources;

class ChargebackCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName()
    {
        return "chargebacks";
    }

    /**
     * Return the resource object
     *
     * @return BaseResource
     */
    protected function getResourceObject()
    {
        return new Chargeback($this->client);
    }
}