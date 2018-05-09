<?php

namespace Mollie\Api\Resources;

class MandateCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName()
    {
        return "mandates";
    }

    /**
     * @return BaseResource
     */
    protected function createResourceObject()
    {
        return new Mandate($this->client);
    }
}