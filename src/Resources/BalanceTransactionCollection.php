<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

class BalanceTransactionCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'balance_transactions';

    /**
     * Resource class name.
     */
    public static string $resource = BalanceTransaction::class;
}
