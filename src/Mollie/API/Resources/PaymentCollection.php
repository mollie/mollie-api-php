<?php

namespace Mollie\Api\Resources;

class PaymentCollection extends BaseCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName()
    {
        return "payments";
    }
}
