<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Payload\UpdatePaymentPayload;
use Mollie\Api\Http\Requests\UpdatePaymentRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Payment;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class UpdatePaymentRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_payment()
    {
        $client = new MockClient([
            UpdatePaymentRequest::class => new MockResponse(200, 'payment'),
        ]);

        $request = new UpdatePaymentRequest('tr_WDqYK6vllg', new UpdatePaymentPayload(
            'Updated payment description',
            'https://example.com/redirect',
        ));

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
        $request = new UpdatePaymentRequest('tr_WDqYK6vllg', new UpdatePaymentPayload(
            'Updated payment description',
            'https://example.com/redirect',
        ));

        $this->assertEquals('payments/tr_WDqYK6vllg', $request->resolveResourcePath());
    }
}
