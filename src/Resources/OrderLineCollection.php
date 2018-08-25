<?php

namespace Mollie\Api\Resources;

class OrderLineCollection extends BaseCollection
{
    /**
     * @return string|null
     */
    public function getCollectionResourceName()
    {
        return null;
    }

    /**
     * Get a specific order line.
     * Returns null if the order line cannot be found.
     *
     * @param  string $lineId
     * @return OrderLine|null
     */
    public function get($lineId)
    {
        foreach ($this as $line) {
            if ($line->id === $lineId) {
                return $line;
            }
        }
        return null;
    }

    /**
     * Check if the order line can be canceled.
     * Returns false if the order cannot be found.
     *
     * @param  string  $lineId
     * @return boolean
     */
    public function isCancelable($lineId)
    {
        $line = $this->get($lineId);
        if (empty($line)) {
            return false;
        }
        return $line->isCancelable;
    }
}
