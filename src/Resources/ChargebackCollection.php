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
}