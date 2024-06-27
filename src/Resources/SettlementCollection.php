<?php

namespace Mollie\Api\Resources;

class SettlementCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "settlements";
    }

    /**
     * @return Settlement
     */
    protected function createResourceObject(): Settlement
    {
        return new Settlement($this->client);
    }
}
