<?php

namespace Mollie\Api\Resources;

class CaptureCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "captures";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Capture::class;
}
