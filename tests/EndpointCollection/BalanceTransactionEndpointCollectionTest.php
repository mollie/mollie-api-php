<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedBalanceTransactionRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceTransaction;
use Mollie\Api\Resources\BalanceTransactionCollection;
use PHPUnit\Framework\TestCase;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;

class BalanceTransactionEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page_for()
    {
        $client = new MockMollieClient([
            GetPaginatedBalanceTransactionRequest::class => new MockResponse(200, 'balance-transactions'),
        ]);

        $balance = new Balance(
            $client,
            $this->createMock(Response::class)
        );
        $balance->id = 'bal_gVMhHKqSSRYJyPsuoPNFH';

        /** @var BalanceTransactionCollection $transactions */
        $transactions = $client->balanceTransactions->pageFor($balance);

        $this->assertInstanceOf(BalanceTransactionCollection::class, $transactions);
        $this->assertGreaterThan(0, $transactions->count());

        foreach ($transactions as $transaction) {
            $this->assertBalanceTransaction($transaction);
        }
    }

    /** @test */
    public function iterator_for()
    {
        $client = new MockMollieClient([
            GetPaginatedBalanceTransactionRequest::class => new MockResponse(200, 'balance-transactions'),
            DynamicGetRequest::class => new MockResponse(200, 'empty-list', 'balance_transactions'),
        ]);

        $balance = new Balance($client, $this->createMock(Response::class));
        $balance->id = 'bal_gVMhHKqSSRYJyPsuoPNFH';

        foreach ($client->balanceTransactions->iteratorFor($balance) as $transaction) {
            $this->assertBalanceTransaction($transaction);
        }
    }

    protected function assertBalanceTransaction(BalanceTransaction $transaction)
    {
        $this->assertInstanceOf(BalanceTransaction::class, $transaction);
        $this->assertEquals('balance_transaction', $transaction->resource);
        $this->assertNotEmpty($transaction->id);
        $this->assertNotEmpty($transaction->type);
        $this->assertNotEmpty($transaction->resultAmount);
        $this->assertNotEmpty($transaction->initialAmount);
        $this->assertNotEmpty($transaction->deductions);
        $this->assertNotEmpty($transaction->context);
    }
}
