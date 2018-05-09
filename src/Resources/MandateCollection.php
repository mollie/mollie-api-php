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
     * Return the resource object
     *
     * @return BaseResource
     */
    protected function getResourceObject()
    {
        return new Mandate($this->client);
    }
}