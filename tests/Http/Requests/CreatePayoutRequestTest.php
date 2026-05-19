<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\CreatePayoutRequest;
use Mollie\Api\Resources\Payout;
use PHPUnit\Framework\TestCase;

class CreatePayoutRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_payout()
    {
        $client = new MockMollieClient([
            CreatePayoutRequest::class => MockResponse::created('payout', 'po_4KgGJJSZpH'),
        ]);

        $request = new CreatePayoutRequest(
            'bal_gVMhHKqSSRYJyPsuoPNFH',
            new Money('EUR', '100.00'),
            'Scheduled payout'
        );

        /** @var Payout */
        $payout = $client->send($request);

        $this->assertTrue($payout->getResponse()->successful());
        $this->assertInstanceOf(Payout::class, $payout);
    }

    /** @test */
    public function it_can_create_payout_without_amount()
    {
        $client = MockMollieClient::fake([
            CreatePayoutRequest::class => MockResponse::created('payout', 'po_4KgGJJSZpH'),
        ]);

        $client->payouts->create([
            'balanceId' => 'bal_gVMhHKqSSRYJyPsuoPNFH',
            'description' => 'Scheduled payout',
        ]);

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $payload = json_decode((string) $pendingRequest->createPsrRequest()->getBody(), true);

            $this->assertSame('bal_gVMhHKqSSRYJyPsuoPNFH', $payload['balanceId']);
            $this->assertArrayNotHasKey('amount', $payload);

            return true;
        });
    }

    /** @test */
    public function it_sends_testmode_in_the_query()
    {
        $client = MockMollieClient::fake([
            CreatePayoutRequest::class => MockResponse::created('payout', 'po_4KgGJJSZpH'),
        ]);

        $client->payouts->create([
            'balanceId' => 'bal_gVMhHKqSSRYJyPsuoPNFH',
            'testmode' => true,
        ]);

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $payload = json_decode((string) $pendingRequest->createPsrRequest()->getBody(), true);

            $this->assertArrayNotHasKey('testmode', $payload);
            $this->assertTrue($pendingRequest->query()->has('testmode'));
            $this->assertEquals('true', $pendingRequest->query()->get('testmode'));

            return true;
        });
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreatePayoutRequest('bal_gVMhHKqSSRYJyPsuoPNFH');

        $this->assertEquals('payouts', $request->resolveResourcePath());
    }
}
