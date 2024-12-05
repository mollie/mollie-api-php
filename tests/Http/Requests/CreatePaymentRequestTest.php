<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Payload\CreatePaymentPayload;
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Payment;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CreatePaymentRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_payment()
    {
        $client = new MockClient([
            CreatePaymentRequest::class => new MockResponse(201, 'payment'),
        ]);

        $payload = new CreatePaymentPayload(
            'Test payment',
            new Money('EUR', '10.00'),
            'https://example.org/redirect',
            'https://example.org/webhook'
        );

        $request = new CreatePaymentRequest($payload);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Payment::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreatePaymentRequest(new CreatePaymentPayload(
            'Test payment',
            new Money('EUR', '10.00'),
            'https://example.org/redirect',
            'https://example.org/webhook'
        ));

        $this->assertEquals('payments', $request->resolveResourcePath());
    }
}
