<?php

namespace Mollie\Api\Resources;

class ProfileCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "profiles";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Profile::class;
    }
}
