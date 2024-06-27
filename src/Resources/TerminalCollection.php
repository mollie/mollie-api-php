<?php

namespace Mollie\Api\Resources;

class TerminalCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "terminals";
    }

    /**
     * @return Terminal
     */
    protected function createResourceObject(): Terminal
    {
        return new Terminal($this->client);
    }
}
