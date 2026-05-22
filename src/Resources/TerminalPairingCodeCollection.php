<?php

namespace Mollie\Api\Resources;

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
