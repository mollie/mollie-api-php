<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreatePayoutRequestFactory;
use Mollie\Api\Http\Requests\CreatePayoutRequest;
use PHPUnit\Framework\TestCase;

class CreatePayoutRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_create_payout_request_object()
    {
        $factory = CreatePayoutRequestFactory::new()
            ->withPayload([
                'balanceId' => 'bal_gVMhHKqSSRYJyPsuoPNFH',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'description' => 'Scheduled payout',
            ]);

        $this->assertInstanceOf(CreatePayoutRequest::class, $factory->create());
    }
}
