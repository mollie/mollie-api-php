<?php

namespace Mollie\Api\Resources;

class RefundCollection extends BaseCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName()
    {
        return "refunds";
    }
}
