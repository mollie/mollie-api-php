<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

class BalanceTransactionCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     *
     * @var string
     */
    public static string $collectionName = "balance_transactions";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = BalanceTransaction::class;
}
