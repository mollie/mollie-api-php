<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Query\PaginatedQuery;
use Mollie\Api\Http\Requests\GetPaginatedBalanceTransactionRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\BalanceTransactionCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaginatedBalanceTransactionRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_balance_transactions()
    {
        $client = new MockClient([
            GetPaginatedBalanceTransactionRequest::class => new MockResponse(200, 'balance-transactions'),
        ]);

        $balanceId = 'bal_gVMhHKqSSRYJyPsuoPNFH';
        $request = new GetPaginatedBalanceTransactionRequest($balanceId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(BalanceTransactionCollection::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $balanceId = 'bal_gVMhHKqSSRYJyPsuoPNFH';
        $request = new GetPaginatedBalanceTransactionRequest($balanceId);

        $this->assertEquals("balances/{$balanceId}/transactions", $request->resolveResourcePath());
    }
}
