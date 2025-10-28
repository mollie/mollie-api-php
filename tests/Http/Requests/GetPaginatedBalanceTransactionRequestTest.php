<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaginatedBalanceTransactionRequest;
use Mollie\Api\Resources\BalanceTransactionCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedBalanceTransactionRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_balance_transactions()
    {
        $client = new MockMollieClient([
            GetPaginatedBalanceTransactionRequest::class => MockResponse::ok('balance-transaction-list'),
        ]);

        $balanceId = 'bal_gVMhHKqSSRYJyPsuoPNFH';
        $request = new GetPaginatedBalanceTransactionRequest($balanceId);

        /** @var BalanceTransactionCollection */
        $balanceTransactions = $client->send($request);

        $this->assertTrue($balanceTransactions->getResponse()->successful());
        $this->assertInstanceOf(BalanceTransactionCollection::class, $balanceTransactions);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $balanceId = 'bal_gVMhHKqSSRYJyPsuoPNFH';
        $request = new GetPaginatedBalanceTransactionRequest($balanceId);

        $this->assertEquals("balances/{$balanceId}/transactions", $request->resolveResourcePath());
    }
}
