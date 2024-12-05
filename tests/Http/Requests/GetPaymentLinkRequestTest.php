<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetPaymentLinkRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\PaymentLink;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaymentLinkRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment_link()
    {
        $client = new MockClient([
            GetPaymentLinkRequest::class => new MockResponse(200, 'payment-link'),
        ]);

        $request = new GetPaymentLinkRequest('pl_4Y0eZitmBnQ5jsBYZIBw');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var PaymentLink */
        $paymentLink = $response->toResource();

        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
        $this->assertEquals('payment-link', $paymentLink->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentLinkId = 'pl_4Y0eZitmBnQ5jsBYZIBw';
        $request = new GetPaymentLinkRequest($paymentLinkId);

        $this->assertEquals("payment-links/{$paymentLinkId}", $request->resolveResourcePath());
    }
}
