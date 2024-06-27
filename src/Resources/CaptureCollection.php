<?php

namespace Mollie\Api\Resources;

class CaptureCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "captures";
    }

    /**
     * @return Capture
     */
    protected function createResourceObject(): Capture
    {
        return new Capture($this->client);
    }
}
