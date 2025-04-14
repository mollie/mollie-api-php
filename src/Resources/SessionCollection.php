<?php

namespace Mollie\Api\Resources;

class SessionCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'sessions';

    /**
     * Resource class name.
     */
    public static string $resource = Session::class;
}
