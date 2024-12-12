<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Payload\CreatePaymentLinkPayload;
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\PaymentLink;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CreatePaymentLinkRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_payment_link()
    {
        $client = new MockClient([
            CreatePaymentLinkRequest::class => new MockResponse(201, 'payment-link'),
        ]);

        $payload = new CreatePaymentLinkPayload(
            'Test payment link',
            new Money('EUR', '10.00')
        );

        $request = new CreatePaymentLinkRequest($payload);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(PaymentLink::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreatePaymentLinkRequest(new CreatePaymentLinkPayload(
            'Test payment link',
            new Money('EUR', '10.00')
        ));

        $this->assertEquals('payment-links', $request->resolveResourcePath());
    }
}