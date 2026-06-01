<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPayoutRequest;
use Mollie\Api\Resources\Payout;
use PHPUnit\Framework\TestCase;

class GetPayoutRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payout()
    {
        $client = new MockMollieClient([
            GetPayoutRequest::class => MockResponse::ok('payout', 'po_4KgGJJSZpH'),
        ]);

        $request = new GetPayoutRequest('po_4KgGJJSZpH');

        /** @var Payout */
        $payout = $client->send($request);

        $this->assertTrue($payout->getResponse()->successful());
        $this->assertInstanceOf(Payout::class, $payout);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $payoutId = 'po_4KgGJJSZpH';
        $request = new GetPayoutRequest($payoutId);

        $this->assertEquals(
            "payouts/{$payoutId}",
            $request->resolveResourcePath()
        );
    }
}
