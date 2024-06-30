<?php

namespace Mollie\Api\Resources;

class CaptureCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "captures";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Capture::class;
    }
}
