<?php

namespace Mollie\Api\Resources;

class TerminalCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "terminals";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Terminal::class;
    }
}
