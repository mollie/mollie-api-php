<?php

namespace Mollie\Api\Resources;

class BalanceCollection extends CursorCollection
{
    public function getCollectionResourceName(): string
    {
        return "balances";
    }

    protected function createResourceObject(): Balance
    {
        return new Balance($this->client);
    }
}
