<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CancelPayoutRequest;
use Mollie\Api\Resources\Payout;
use PHPUnit\Framework\TestCase;

class CancelPayoutRequestTest extends TestCase
{
    /** @test */
    public function it_can_cancel_payout()
    {
        $client = new MockMollieClient([
            CancelPayoutRequest::class => MockResponse::ok('payout', 'po_4KgGJJSZpH'),
        ]);

        $request = new CancelPayoutRequest('po_4KgGJJSZpH');

        /** @var Payout */
        $payout = $client->send($request);

        $this->assertTrue($payout->getResponse()->successful());
        $this->assertInstanceOf(Payout::class, $payout);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $payoutId = 'po_4KgGJJSZpH';
        $request = new CancelPayoutRequest($payoutId);

        $this->assertEquals(
            "payouts/{$payoutId}",
            $request->resolveResourcePath()
        );
    }
}
