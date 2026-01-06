<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\ListConnectBalanceTransfersRequest;
use Mollie\Api\Resources\ConnectBalanceTransferCollection;
use PHPUnit\Framework\TestCase;

class ListConnectBalanceTransfersRequestTest extends TestCase
{
    /** @test */
    public function it_can_list_connect_balance_transfers()
    {
        $client = new MockMollieClient([
            ListConnectBalanceTransfersRequest::class => MockResponse::ok('connect-balance-transfer-list'),
        ]);

        $request = new ListConnectBalanceTransfersRequest();

        /** @var ConnectBalanceTransferCollection */
        $balanceTransferCollection = $client->send($request);

        $this->assertTrue($balanceTransferCollection->getResponse()->successful());
        $this->assertInstanceOf(ConnectBalanceTransferCollection::class, $balanceTransferCollection);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new ListConnectBalanceTransfersRequest();

        $this->assertEquals('connect/balance-transfers', $request->resolveResourcePath());
    }
}
