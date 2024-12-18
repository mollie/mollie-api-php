<?php

namespace Mollie\Api\Resources;

class ProfileCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'profiles';

    /**
     * Resource class name.
     */
    public static string $resource = Profile::class;
}
