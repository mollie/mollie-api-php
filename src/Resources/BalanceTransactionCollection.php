<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

class BalanceTransactionCollection extends CursorCollection
{
    /**
     * @inheritDoc
     */
    public static function getCollectionResourceName(): string
    {
        return "balance_transactions";
    }

    /**
     * @inheritDoc
     */
    public static function getResourceClass(): string
    {
        return BalanceTransaction::class;
    }
}
