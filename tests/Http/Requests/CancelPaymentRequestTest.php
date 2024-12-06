<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\CancelPaymentRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Payment;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CancelPaymentRequestTest extends TestCase
{
    /** @test */
    public function it_can_cancel_payment()
    {
        $client = new MockClient([
            CancelPaymentRequest::class => new MockResponse(200, 'payment'),
        ]);

        $paymentId = 'tr_WDqYK6vllg';
        $request = new CancelPaymentRequest($paymentId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Payment::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_WDqYK6vllg';
        $request = new CancelPaymentRequest($paymentId);

        $this->assertEquals(
            "payments/{$paymentId}",
            $request->resolveResourcePath()
        );
    }
}
