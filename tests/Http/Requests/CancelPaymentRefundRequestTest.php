<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\CancelPaymentRefundRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class CancelPaymentRefundRequestTest extends TestCase
{
    /** @test */
    public function it_can_cancel_payment_refund()
    {
        $client = new MockClient([
            CancelPaymentRefundRequest::class => new MockResponse(204, ''),
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
