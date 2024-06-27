<?php

namespace Mollie\Api\Resources;

class MandateCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "mandates";
    }

    /**
     * @return Mandate
     */
    protected function createResourceObject(): Mandate
    {
        return new Mandate($this->client);
    }

    /**
     * @param string $status
     * @return MandateCollection
     */
    public function whereStatus($status): self
    {
        return $this->filter(fn (Mandate $mandate) => $mandate->status === $status);
    }
}
