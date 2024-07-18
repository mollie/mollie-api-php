<?php

namespace Mollie\Api\Resources;

class MandateCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "mandates";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Mandate::class;

    /**
     * @param string $status
     * @return MandateCollection
     */
    public function whereStatus($status): self
    {
        return $this->filter(fn (Mandate $mandate) => $mandate->status === $status);
    }
}
