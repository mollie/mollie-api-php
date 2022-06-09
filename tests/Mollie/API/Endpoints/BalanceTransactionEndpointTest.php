<?php

declare(strict_types=1);

namespace Tests\Mollie\API\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class BalanceTransactionEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;
    use AmountObjectTestHelpers;

    public function testGetBalanceTransactions()
    {
        // /v2/balances/*/transactions
        // $api->balanceTransactions->forBalance();
        // $balance->transactions();
        $this->markTestIncomplete("TBI BalanceTransactionEndpointTest");
    }

    public function testGetPrimaryBalanceTransactions()
    {
        // /v2/balances/primary/transactions
        // $api->balanceTransactions->forPrimary();
        // $api->balances->primary()->transactions(); (2 calls)
        // $api->balances->primaryTransactions(); (1 call)
        $this->markTestIncomplete("TBI BalanceTransactionEndpointTest");
    }
}
