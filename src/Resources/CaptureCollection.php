<?php

namespace Mollie\Api\Resources;

class CaptureCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'captures';

    /**
     * Resource class name.
     */
    public static string $resource = Capture::class;
}
