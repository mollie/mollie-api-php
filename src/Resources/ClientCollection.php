<?php

namespace Mollie\Api\Resources;

class ClientCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "clients";
    }

    /**
     * @return Client
     */
    protected function createResourceObject(): Client
    {
        return new Client($this->client);
    }
}
