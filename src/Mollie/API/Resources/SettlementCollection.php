<?php

namespace Mollie\Api\Resources;

class SettlementCollection extends BaseCollection
{

    /**
     * @return string
     */
    public function getCollectionResourceName()
    {
        return "settlements";
    }
}