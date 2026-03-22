<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\TransferParty;
use Mollie\Api\Http\Requests\CreateConnectBalanceTransferRequest;
use Mollie\Api\Resources\ConnectBalanceTransfer;
use Mollie\Api\Types\ConnectBalanceTransferCategory;
use PHPUnit\Framework\TestCase;

class CreateConnectBalanceTransferRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_connect_balance_transfer()
    {
        $client = new MockMollieClient([
            CreateConnectBalanceTransferRequest::class => MockResponse::created('connect-balance-transfer'),
        ]);

        $request = new CreateConnectBalanceTransferRequest(
            new Money('EUR', '100.00'),
            'Transfer from balance A to balance B',
            new TransferParty(
                'org_12345678',
                'Payment from Organization A'
            ),
            new TransferParty(
                'org_87654321',
                'Payment to Organization B'
            ),
            ConnectBalanceTransferCategory::MANUAL_CORRECTION,
            [
                'order_id' => '12345',
                'description' => 'Manual correction for order',
            ]
        );

        /** @var ConnectBalanceTransfer */
        $balanceTransfer = $client->send($request);

        $this->assertTrue($balanceTransfer->getResponse()->successful());
        $this->assertInstanceOf(ConnectBalanceTransfer::class, $balanceTransfer);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreateConnectBalanceTransferRequest(
            new Money('EUR', '100.00'),
            'Transfer from balance A to balance B',
            new TransferParty(
                'org_12345678',
                'Payment from Organization A'
            ),
            new TransferParty(
                'org_87654321',
                'Payment to Organization B'
            ),
            ConnectBalanceTransferCategory::PURCHASE
        );

        $this->assertEquals('connect/balance-transfers', $request->resolveResourcePath());
    }
}
