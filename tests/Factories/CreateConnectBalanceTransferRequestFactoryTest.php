<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreateConnectBalanceTransferRequestFactory;
use Mollie\Api\Http\Requests\CreateConnectBalanceTransferRequest;
use PHPUnit\Framework\TestCase;

class CreateConnectBalanceTransferRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_connect_balance_transfer_request_object_with_full_data()
    {
        $request = CreateConnectBalanceTransferRequestFactory::new()
            ->withPayload([
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
                'metadata' => [
                    'order_id' => '12345',
                    'description' => 'Manual correction for order',
                ],
            ])
            ->create();

        $this->assertInstanceOf(CreateConnectBalanceTransferRequest::class, $request);
        $this->assertEquals('connect/balance-transfers', $request->resolveResourcePath());
        $this->assertEquals('POST', $request->getMethod());
    }

    /** @test */
    public function create_returns_connect_balance_transfer_request_object_with_minimal_data()
    {
        $request = CreateConnectBalanceTransferRequestFactory::new()
            ->withPayload([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '50.00',
                ],
                'description' => 'Minimal transfer',
                'source' => [
                    'type' => 'organization',
                    'id' => 'org_source123',
                    'description' => 'Minimal transfer from source',
                ],
                'destination' => [
                    'type' => 'organization',
                    'id' => 'org_destination456',
                    'description' => 'Minimal transfer to destination',
                ],
                'category' => 'purchase',
            ])
            ->create();

        $this->assertInstanceOf(CreateConnectBalanceTransferRequest::class, $request);
        $this->assertEquals('connect/balance-transfers', $request->resolveResourcePath());
        $this->assertEquals('POST', $request->getMethod());
    }
}
