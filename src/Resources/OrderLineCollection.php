<?php

namespace Mollie\Api\Resources;

class OrderLineCollection extends BaseCollection
{
    /**
     * Get a specific order line.
     * Returns null if the order line cannot be found.
     *
     * @param  string $lineId
     * @return OrderLine|null
     */
    public function get($lineId): ?OrderLine
    {
        foreach ($this as $line) {
            if ($line->id === $lineId) {
                return $line;
            }
        }

        return null;
    }
}
