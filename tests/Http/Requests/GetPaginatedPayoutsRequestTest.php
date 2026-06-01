<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPayoutsRequest;
use Mollie\Api\Resources\Payout;
use Mollie\Api\Resources\PayoutCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedPayoutsRequestTest extends TestCase
{
    /** @test */
    public function it_can_list_payouts()
    {
        $client = new MockMollieClient([
            GetPaginatedPayoutsRequest::class => MockResponse::ok('payout-list'),
        ]);

        $request = new GetPaginatedPayoutsRequest;

        /** @var PayoutCollection */
        $payouts = $client->send($request);

        $this->assertTrue($payouts->getResponse()->successful());

        foreach ($payouts as $payout) {
            $this->assertInstanceOf(Payout::class, $payout);
            $this->assertEquals('payout', $payout->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_payouts()
    {
        $client = new MockMollieClient([
            GetPaginatedPayoutsRequest::class => MockResponse::ok('payout-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('payout-list'),
                MockResponse::ok('empty-list', 'payouts'),
            ),
        ]);

        $request = (new GetPaginatedPayoutsRequest)->useIterator();

        /** @var PayoutCollection */
        $payouts = $client->send($request);
        $this->assertTrue($payouts->getResponse()->successful());

        foreach ($payouts as $payout) {
            $this->assertInstanceOf(Payout::class, $payout);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedPayoutsRequest;

        $this->assertEquals('payouts', $request->resolveResourcePath());
    }
}
