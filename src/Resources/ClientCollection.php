<?php

namespace Mollie\Api\Resources;

class ClientCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "clients";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Client::class;
    }
}
