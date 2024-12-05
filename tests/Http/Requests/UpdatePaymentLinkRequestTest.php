<?php

namespace Tests\Http\Requests;

use DateTime;
use Mollie\Api\Http\Payload\UpdatePaymentLinkPayload;
use Mollie\Api\Http\Requests\UpdatePaymentLinkRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\PaymentLink;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class UpdatePaymentLinkRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_payment_link()
    {
        $client = new MockClient([
            UpdatePaymentLinkRequest::class => new MockResponse(200, 'payment-link'),
        ]);

        $request = new UpdatePaymentLinkRequest('pl_4Y0eZitmBnQ5jsBYZIBw', new UpdatePaymentLinkPayload(
            'Updated payment link',
        ));

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
        $request = new UpdatePaymentLinkRequest('pl_4Y0eZitmBnQ5jsBYZIBw', new UpdatePaymentLinkPayload(
            'Updated payment link',
        ));

        $this->assertEquals('payment-links/pl_4Y0eZitmBnQ5jsBYZIBw', $request->resolveResourcePath());
    }
}
