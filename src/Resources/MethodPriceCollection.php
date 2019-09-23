<?php

namespace Mollie\Api\Resources;

class MethodPriceCollection extends BaseCollection
{
    /**
     * @return string|null
     */
    public function getCollectionResourceName()
    {
        return null;
    }

    /**
     * Get a specific MethodPrice.
     * Returns null if the MethodPrice cannot be found.
     *
     * @param  string $description
     * @return MethodPrice|null
     */
    public function get($description)
    {
        foreach ($this as $methodPrice) {
            if ($methodPrice->description === $description) {
                return $methodPrice;
            }
        }
        return null;
    }
}
