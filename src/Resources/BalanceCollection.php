<?php

namespace Mollie\Api\Resources;

class BalanceCollection extends CursorCollection
{
    public static function getCollectionResourceName(): string
    {
        return "balances";
    }

    public static function getResourceClass(): string
    {
        return Balance::class;
    }
}
