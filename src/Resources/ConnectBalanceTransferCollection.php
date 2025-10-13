<?php

namespace Mollie\Api\Resources;

class ConnectBalanceTransferCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'connect_balance_transfers';

    /**
     * Resource class name.
     */
    public static string $resource = ConnectBalanceTransfer::class;
}
