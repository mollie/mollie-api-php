<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

class BalanceTransactionCollection extends CursorCollection
{
    /**
     * @inheritDoc
     */
    public function getCollectionResourceName(): string
    {
        return "balance_transactions";
    }

    /**
     * @inheritDoc
     */
    protected function createResourceObject(): BalanceTransaction
    {
        return new BalanceTransaction($this->client);
    }
}
