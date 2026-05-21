<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @extends CursorCollection<\Mollie\Api\Resources\TerminalPairingCode>
 */
class TerminalPairingCodeCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'terminal-pairing-codes';

    /**
     * Resource class name.
     */
    public static string $resource = TerminalPairingCode::class;
}
