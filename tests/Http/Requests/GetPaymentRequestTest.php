<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Query\GetPaymentQuery;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Payment;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaymentRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment()
    {
        $client = new MockClient([
            GetPaymentRequest::class => new MockResponse(200, 'payment'),
        ]);

        $paymentId = 'tr_WDqYK6vllg';
        $request = new GetPaymentRequest($paymentId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Payment */
        $payment = $response->toResource();

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('payment', $payment->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_WDqYK6vllg';
        $request = new GetPaymentRequest($paymentId);

        $this->assertEquals("payments/{$paymentId}", $request->resolveResourcePath());
    }
}
