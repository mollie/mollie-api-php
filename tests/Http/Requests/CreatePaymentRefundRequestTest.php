<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Payload\CreateRefundPaymentPayload;
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Refund;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CreatePaymentRefundRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_payment_refund()
    {
        $client = new MockClient([
            CreatePaymentRefundRequest::class => new MockResponse(201, 'refund'),
        ]);

        $paymentId = 'tr_WDqYK6vllg';
        $payload = new CreateRefundPaymentPayload(
            'Order cancellation',
            new Money('EUR', '10.00')
        );

        $request = new CreatePaymentRefundRequest($paymentId, $payload);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Refund::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_WDqYK6vllg';
        $payload = new CreateRefundPaymentPayload(
            'Order cancellation',
            new Money('EUR', '10.00')
        );

        $request = new CreatePaymentRefundRequest($paymentId, $payload);

        $this->assertEquals(
            "payments/{$paymentId}/refunds",
            $request->resolveResourcePath()
        );
    }
}
