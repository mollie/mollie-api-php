<?php

declare(strict_types=1);

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetBalanceRequest;
use Mollie\Api\Http\Requests\GetPaginatedBalanceRequest;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\Traits\AmountObjectTestHelpers;
use Tests\Fixtures\Traits\LinkObjectTestHelpers;

class BalanceEndpointCollectionTest extends TestCase
{
    use AmountObjectTestHelpers;
    use LinkObjectTestHelpers;

    /** @test */
    public function get()
    {
        $client = new MockClient([
            GetBalanceRequest::class => new MockResponse(200, 'balance'),
        ]);

        /** @var Balance $balance */
        $balance = $client->balances->get('bal_gVMhHKqSSRYJyPsuoPNFH');

        $this->assertBalance(
            $balance,
            'bal_gVMhHKqSSRYJyPsuoPNFH',
        );
    }

    /** @test */
    public function primary()
    {
        $client = new MockClient([
            GetBalanceRequest::class => new MockResponse(200, 'balance'),
        ]);

        /** @var Balance $balance */
        $balance = $client->balances->primary();

        $this->assertBalance(
            $balance,
            'bal_gVMhHKqSSRYJyPsuoPNFH',
        );
    }

    /** @test */
    public function page()
    {
        $client = new MockClient([
            GetPaginatedBalanceRequest::class => new MockResponse(200, 'balance-list'),
        ]);

        /** @var BalanceCollection $balances */
        $balances = $client->balances->page();

        $this->assertInstanceOf(BalanceCollection::class, $balances);
        $this->assertEquals(2, $balances->count());
        $this->assertCount(2, $balances);

        $this->assertLinkObject(
            '...',
            'text/html',
            $balances->_links->documentation
        );

        $this->assertLinkObject(
            'https://api.mollie.com/v2/balances?limit=5',
            'application/hal+json',
            $balances->_links->self
        );

        $this->assertLinkObject(
            '...',
            'application/hal+json',
            $balances->_links->next
        );

        $this->assertBalance(
            $balances[0],
            'bal_gVMhHKqSSRYJyPsuoPNFH',
        );
        $this->assertBalance(
            $balances[1],
            'bal_gVMhHKqSSRYJyPsuoPABC',
        );
    }

    /** @test */
    public function iterate()
    {
        $client = new MockClient([
            GetPaginatedBalanceRequest::class => new MockResponse(200, 'balance-list'),
            DynamicGetRequest::class => new MockResponse(200, 'empty-list', 'balances'),
        ]);

        foreach ($client->balances->iterator() as $balance) {
            $this->assertInstanceOf(Balance::class, $balance);
        }
    }

    /**
     * @return void
     */
    protected function assertBalance(
        Balance $balance,
        string $balanceId
    ) {
        $this->assertInstanceOf(Balance::class, $balance);
        $this->assertEquals('balance', $balance->resource);
        $this->assertEquals($balanceId, $balance->id);

        $this->assertNotEmpty($balance->mode);
        $this->assertNotEmpty($balance->createdAt);
        $this->assertNotEmpty($balance->currency);
        $this->assertNotNull($balance->availableAmount);
        $this->assertNotNull($balance->incomingAmount);
        $this->assertNotNull($balance->outgoingAmount);
        $this->assertNotEmpty($balance->transferFrequency);
        $this->assertNotNull($balance->transferThreshold);
        $this->assertNotEmpty($balance->transferReference);
        $this->assertNotNull($balance->transferDestination);
        $this->assertNotNull($balance->_links->self);
    }
}
