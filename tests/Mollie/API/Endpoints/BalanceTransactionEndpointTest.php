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
        $this->mockApiCall(
            new Request("GET", "/v2/balances/*/transactions"),
            new Response(
                200,
                [],
                ''
            )
        );


        // $api->balanceTransactions->forBalance();
        // $balance->transactions();
        $this->markTestIncomplete("TBI BalanceTransactionEndpointTest");
    }

    public function testGetPrimaryBalanceTransactions()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/balances/me/transactions"),
            new Response(
                200,
                [],
                ''
            )
        );
        // /v2/balances/primary/transactions
        // $api->balanceTransactions->forPrimary(); (1 call)
        // $api->balances->primaryTransactions(); (1 call, proxy)
        // $api->balances->primary()->transactions(); (2 calls)
        $this->markTestIncomplete("TBI BalanceTransactionEndpointTest");
    }
}
