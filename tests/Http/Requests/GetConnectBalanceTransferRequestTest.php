<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetConnectBalanceTransferRequest;
use Mollie\Api\Resources\ConnectBalanceTransfer;
use PHPUnit\Framework\TestCase;

class GetConnectBalanceTransferRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_connect_balance_transfer()
    {
        $client = new MockMollieClient([
            GetConnectBalanceTransferRequest::class => MockResponse::ok('connect-balance-transfer'),
        ]);

        $request = new GetConnectBalanceTransferRequest('cbt_4KgGJJSZpH');

        /** @var ConnectBalanceTransfer */
        $balanceTransfer = $client->send($request);

        $this->assertTrue($balanceTransfer->getResponse()->successful());
        $this->assertInstanceOf(ConnectBalanceTransfer::class, $balanceTransfer);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetConnectBalanceTransferRequest('cbt_4KgGJJSZpH');

        $this->assertEquals('connect/balance-transfers/cbt_4KgGJJSZpH', $request->resolveResourcePath());
    }
}
