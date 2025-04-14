<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CancelPaymentRefundRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class CancelPaymentRefundRequestTest extends TestCase
{
    /** @test */
    public function it_can_cancel_payment_refund()
    {
        $client = new MockMollieClient([
            CancelPaymentRefundRequest::class => MockResponse::noContent(),
        ]);

        $paymentId = 'tr_7UhSN1zuXS';
        $refundId = 're_4qqhO89gsT';

        $request = new CancelPaymentRefundRequest($paymentId, $refundId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertEquals(204, $response->status());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_7UhSN1zuXS';
        $refundId = 're_4qqhO89gsT';

        $request = new CancelPaymentRefundRequest($paymentId, $refundId);

        $this->assertEquals(
            "payments/{$paymentId}/refunds/{$refundId}",
            $request->resolveResourcePath()
        );
    }
}
