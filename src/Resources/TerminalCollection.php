<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @extends CursorCollection<\Mollie\Api\Resources\Terminal>
 */
class TerminalCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'terminals';

    /**
     * Resource class name.
     */
    public static string $resource = Terminal::class;
}
