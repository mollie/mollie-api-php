<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaymentLinkRequest;
use Mollie\Api\Resources\PaymentLink;
use PHPUnit\Framework\TestCase;

class GetPaymentLinkRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment_link()
    {
        $client = new MockMollieClient([
            GetPaymentLinkRequest::class => MockResponse::ok('payment-link'),
        ]);

        $request = new GetPaymentLinkRequest('pl_4Y0eZitmBnQ5jsBYZIBw');

        /** @var PaymentLink */
        $paymentLink = $client->send($request);

        $this->assertTrue($paymentLink->getResponse()->successful());
        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
    }

    /** @test */
    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentLinkId = 'pl_4Y0eZitmBnQ5jsBYZIBw';
        $request = new GetPaymentLinkRequest($paymentLinkId);

        $this->assertEquals("payment-links/{$paymentLinkId}", $request->resolveResourcePath());
    }
}
