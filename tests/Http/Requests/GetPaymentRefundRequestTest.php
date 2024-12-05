<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetPaymentRefundRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Refund;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaymentRefundRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment_refund()
    {
        $client = new MockClient([
            GetPaymentRefundRequest::class => new MockResponse(200, 'refund'),
        ]);

        $paymentId = 'tr_WDqYK6vllg';
        $refundId = 're_4qqhO89gsT';
        $request = new GetPaymentRefundRequest($paymentId, $refundId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Refund */
        $refund = $response->toResource();

        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals('refund', $refund->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_WDqYK6vllg';
        $refundId = 're_4qqhO89gsT';
        $request = new GetPaymentRefundRequest($paymentId, $refundId);

        $this->assertEquals("payments/{$paymentId}/refunds/{$refundId}", $request->resolveResourcePath());
    }
}
