<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CancelPayoutRequest;
use Mollie\Api\Http\Requests\CreatePayoutRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPayoutRequest;
use Mollie\Api\Http\Requests\GetPaginatedPayoutsRequest;
use Mollie\Api\Resources\Payout;
use Mollie\Api\Resources\PayoutCollection;
use PHPUnit\Framework\TestCase;

class PayoutEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedPayoutsRequest::class => MockResponse::ok('payout-list'),
        ]);

        /** @var PayoutCollection $payouts */
        $payouts = $client->payouts->page();

        $this->assertInstanceOf(PayoutCollection::class, $payouts);
        $this->assertGreaterThan(0, $payouts->count());
        $this->assertGreaterThan(0, count($payouts));
    }

    /** @test */
    public function iterator()
    {
        $client = new MockMollieClient([
            GetPaginatedPayoutsRequest::class => MockResponse::ok('payout-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'payouts'),
        ]);

        foreach ($client->payouts->iterator() as $payout) {
            $this->assertPayout($payout);
        }
    }

    /** @test */
    public function create()
    {
        $client = new MockMollieClient([
            CreatePayoutRequest::class => MockResponse::created('payout', 'po_4KgGJJSZpH'),
        ]);

        /** @var Payout $payout */
        $payout = $client->payouts->create([
            'balanceId' => 'bal_gVMhHKqSSRYJyPsuoPNFH',
            'amount' => [
                'currency' => 'EUR',
                'value' => '100.00',
            ],
            'description' => 'Scheduled payout',
        ]);

        $this->assertPayout($payout);
    }

    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetPayoutRequest::class => MockResponse::ok('payout', 'po_4KgGJJSZpH'),
        ]);

        /** @var Payout $payout */
        $payout = $client->payouts->get('po_4KgGJJSZpH');

        $this->assertPayout($payout);
    }

    /** @test */
    public function cancel()
    {
        $client = new MockMollieClient([
            CancelPayoutRequest::class => MockResponse::ok('payout', 'po_4KgGJJSZpH'),
        ]);

        /** @var Payout $payout */
        $payout = $client->payouts->cancel('po_4KgGJJSZpH');

        $this->assertPayout($payout);
    }

    private function assertPayout(Payout $payout): void
    {
        $this->assertInstanceOf(Payout::class, $payout);
        $this->assertEquals('payout', $payout->resource);
        $this->assertEquals('po_4KgGJJSZpH', $payout->id);
        $this->assertNotEmpty($payout->balanceId);
        $this->assertNotEmpty($payout->amount);
        $this->assertNotEmpty($payout->status);
        $this->assertNotEmpty($payout->createdAt);
    }
}
