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
                    'balanceId' => 'bal_gVMhHKqSSRYJyPsuoPABC',
                ],
                'destination' => [
                    'balanceId' => 'bal_gVMhHKqSSRYJyPsuoPXYZ',
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
                    'balanceId' => 'bal_source123',
                ],
                'destination' => [
                    'balanceId' => 'bal_destination456',
                ],
            ])
            ->create();

        $this->assertInstanceOf(CreateConnectBalanceTransferRequest::class, $request);
        $this->assertEquals('connect/balance-transfers', $request->resolveResourcePath());
        $this->assertEquals('POST', $request->getMethod());
    }
}
