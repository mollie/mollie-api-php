<?php

declare(strict_types=1);

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetBalanceRequest;
use Mollie\Api\Http\Requests\GetPaginatedBalanceRequest;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceCollection;
use Mollie\Api\Types\BalanceTransferFrequency;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\Traits\AmountObjectTestHelpers;
use Tests\Fixtures\Traits\LinkObjectTestHelpers;

class BalanceEndpointCollectionTest extends TestCase
{
    use AmountObjectTestHelpers;
    use LinkObjectTestHelpers;

    public function testListBalances()
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
            'https://docs.mollie.com/reference/v2/balances-api/list-balances',
            'text/html',
            $balances->_links->documentation
        );

        $this->assertLinkObject(
            'https://api.mollie.com/v2/balances?limit=5',
            'application/hal+json',
            $balances->_links->self
        );

        $this->assertLinkObject(
            'https://api.mollie.com/v2/balances?from=bal_gVMhHKqSSRYJyPsuoPABC&limit=5',
            'application/hal+json',
            $balances->_links->next
        );

        /** @var Balance $balanceA */
        $balanceA = $balances[0];

        /** @var Balance $balanceB */
        $balanceB = $balances[1];

        $this->assertBalance(
            $balanceA,
            'bal_gVMhHKqSSRYJyPsuoPNFH',
            '2019-01-10T12:06:28+00:00',
            BalanceTransferFrequency::DAILY,
            '40.00',
            (object) [
                'type' => 'bank-account',
                'beneficiaryName' => 'Jack Bauer',
                'bankAccount' => 'NL53INGB0654422370',
                'bankAccountId' => 'bnk_jrty3f',
            ]
        );
        $this->assertBalance(
            $balanceB,
            'bal_gVMhHKqSSRYJyPsuoPABC',
            '2019-01-10T10:23:41+00:00',
            BalanceTransferFrequency::TWICE_A_MONTH,
            '5.00',
            (object) [
                'type' => 'bank-account',
                'beneficiaryName' => 'Jack Bauer',
                'bankAccount' => 'NL97MOLL6351480700',
                'bankAccountId' => 'bnk_jrty3e',
            ]
        );
    }

    public function testIterateBalances()
    {
        $client = new MockClient([
            GetPaginatedBalanceRequest::class => new MockResponse(200, 'balance-list'),
            DynamicGetRequest::class => new MockResponse(200, 'empty-balance-list'),
        ]);

        foreach ($client->balances->iterator() as $balance) {
            $this->assertInstanceOf(Balance::class, $balance);
        }
    }

    public function testGetBalance()
    {
        $client = new MockClient([
            GetBalanceRequest::class => new MockResponse(200, 'balance-get'),
        ]);

        /** @var Balance $balance */
        $balance = $client->balances->get('bal_gVMhHKqSSRYJyPsuoPNFH');

        $this->assertBalance(
            $balance,
            'bal_gVMhHKqSSRYJyPsuoPNFH',
            '2019-01-10T10:23:41+00:00',
            BalanceTransferFrequency::TWICE_A_MONTH,
            '5.00',
            (object) [
                'type' => 'bank-account',
                'beneficiaryName' => 'Jack Bauer',
                'bankAccount' => 'NL53INGB0654422370',
                'bankAccountId' => 'bnk_jrty3f',
            ]
        );
    }

    public function testGetPrimaryBalance()
    {
        $client = new MockClient([
            GetBalanceRequest::class => new MockResponse(200, 'balance-get'),
        ]);

        /** @var Balance $balance */
        $balance = $client->balances->primary();

        $this->assertBalance(
            $balance,
            'bal_gVMhHKqSSRYJyPsuoPNFH',
            '2019-01-10T10:23:41+00:00',
            BalanceTransferFrequency::TWICE_A_MONTH,
            '5.00',
            (object) [
                'type' => 'bank-account',
                'beneficiaryName' => 'Jack Bauer',
                'bankAccount' => 'NL53INGB0654422370',
                'bankAccountId' => 'bnk_jrty3f',
            ]
        );
    }

    /**
     * @return void
     */
    protected function assertBalance(
        Balance $balance,
        string $balanceId,
        string $createdAt,
        string $transferFrequency,
        string $thresholdValue,
        \stdClass $destination
    ) {
        $this->assertInstanceOf(Balance::class, $balance);
        $this->assertEquals('balance', $balance->resource);
        $this->assertEquals($balanceId, $balance->id);

        $this->assertEquals('live', $balance->mode);
        $this->assertEquals($createdAt, $balance->createdAt);
        $this->assertEquals('EUR', $balance->currency);
        $this->assertAmountObject('0.00', 'EUR', $balance->availableAmount);
        $this->assertAmountObject('0.00', 'EUR', $balance->incomingAmount);
        $this->assertAmountObject('0.00', 'EUR', $balance->outgoingAmount);
        $this->assertEquals($transferFrequency, $balance->transferFrequency);
        $this->assertAmountObject($thresholdValue, 'EUR', $balance->transferThreshold);
        $this->assertEquals('Mollie payout', $balance->transferReference);
        $this->assertEquals($destination, $balance->transferDestination);

        $this->assertLinkObject(
            "https://api.mollie.com/v2/balances/{$balanceId}",
            'application/hal+json',
            $balance->_links->self
        );
    }
}
