<?php

namespace Mollie\Api\Resources;

class Shipment extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * Id of the shipment.
     *
     * @var string
     */
    public $id;

    /**
     * Get the line value objects
     *
     * @return OrderLineCollection
     */
    public function lines()
    {
        $lines  = new OrderLineCollection(count($this->lines), null);
        foreach ($this->lines as $line) {
            $lines->append(ResourceFactory::createFromApiResult($line, new OrderLine($this->client)));
        }

        return $lines;
    }
}
