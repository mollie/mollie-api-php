<?php

namespace Mollie\Api\Resources;

class TerminalCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "terminals";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Terminal::class;
}
