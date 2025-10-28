<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreateConnectBalanceTransferRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetConnectBalanceTransferRequest;
use Mollie\Api\Http\Requests\ListConnectBalanceTransfersRequest;
use Mollie\Api\Resources\ConnectBalanceTransfer;
use Mollie\Api\Resources\ConnectBalanceTransferCollection;
use PHPUnit\Framework\TestCase;

class ConnectBalanceTransferEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create()
    {
        $client = new MockMollieClient([
            CreateConnectBalanceTransferRequest::class => MockResponse::created('connect-balance-transfer'),
        ]);

        /** @var ConnectBalanceTransfer $balanceTransfer */
        $balanceTransfer = $client->connectBalanceTransfers->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => '100.00',
            ],
            'description' => 'Transfer from balance A to balance B',
            'source' => [
                'type' => 'organization',
                'id' => 'org_12345678',
                'description' => 'Payment from Organization A',
            ],
            'destination' => [
                'type' => 'organization',
                'id' => 'org_87654321',
                'description' => 'Payment to Organization B',
            ],
            'category' => 'manual_correction',
        ]);

        $this->assertConnectBalanceTransfer($balanceTransfer);
    }

    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetConnectBalanceTransferRequest::class => MockResponse::ok('connect-balance-transfer'),
        ]);

        /** @var ConnectBalanceTransfer $balanceTransfer */
        $balanceTransfer = $client->connectBalanceTransfers->get('cbt_4KgGJJSZpH');

        $this->assertConnectBalanceTransfer($balanceTransfer);
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            ListConnectBalanceTransfersRequest::class => MockResponse::ok('connect-balance-transfer-list'),
        ]);

        /** @var ConnectBalanceTransferCollection $balanceTransfers */
        $balanceTransfers = $client->connectBalanceTransfers->page();

        $this->assertInstanceOf(ConnectBalanceTransferCollection::class, $balanceTransfers);
        $this->assertGreaterThan(0, $balanceTransfers->count());
        $this->assertGreaterThan(0, count($balanceTransfers));
    }

    /** @test */
    public function iterator()
    {
        $client = new MockMollieClient([
            ListConnectBalanceTransfersRequest::class => MockResponse::ok('connect-balance-transfer-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'connect_balance_transfers'),
        ]);

        foreach ($client->connectBalanceTransfers->iterator() as $balanceTransfer) {
            $this->assertConnectBalanceTransfer($balanceTransfer);
        }
    }

    private function assertConnectBalanceTransfer(ConnectBalanceTransfer $balanceTransfer): void
    {
        $this->assertInstanceOf(ConnectBalanceTransfer::class, $balanceTransfer);
        $this->assertEquals('connect-balance-transfer', $balanceTransfer->resource);
        $this->assertNotEmpty($balanceTransfer->id);
        $this->assertNotEmpty($balanceTransfer->amount);
        $this->assertNotEmpty($balanceTransfer->source);
        $this->assertNotEmpty($balanceTransfer->destination);
        $this->assertNotEmpty($balanceTransfer->description);
        $this->assertNotEmpty($balanceTransfer->createdAt);
    }
}
