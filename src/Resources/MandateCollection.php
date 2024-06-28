<?php

namespace Mollie\Api\Resources;

class MandateCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "mandates";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Mandate::class;
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
