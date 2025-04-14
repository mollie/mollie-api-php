<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaymentRefundRequest;
use Mollie\Api\Resources\Refund;
use PHPUnit\Framework\TestCase;

class GetPaymentRefundRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment_refund()
    {
        $client = new MockMollieClient([
            GetPaymentRefundRequest::class => MockResponse::ok('refund'),
        ]);

        $paymentId = 'tr_WDqYK6vllg';
        $refundId = 're_4qqhO89gsT';
        $request = new GetPaymentRefundRequest($paymentId, $refundId);

        /** @var Refund */
        $refund = $client->send($request);

        $this->assertTrue($refund->getResponse()->successful());
        $this->assertInstanceOf(Refund::class, $refund);
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
