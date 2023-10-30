<?php

namespace Mollie\Api\Resources;

class MandateCollection extends CursorCollection
{
    public function getLazyCollectionName(): string
    {
        return LazyMandateCollection::class;
    }

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

    /**
     * @param string $status
     * @return array|\Mollie\Api\Resources\MandateCollection
     */
    public function whereStatus($status)
    {
        return $this->filter(fn (Mandate $mandate) => $mandate->status === $status);
    }
}
