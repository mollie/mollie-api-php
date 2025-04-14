<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\UpdatePaymentLinkRequest;
use Mollie\Api\Resources\PaymentLink;
use PHPUnit\Framework\TestCase;

class UpdatePaymentLinkRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_payment_link()
    {
        $client = new MockMollieClient([
            UpdatePaymentLinkRequest::class => MockResponse::ok('payment-link'),
        ]);

        $request = new UpdatePaymentLinkRequest('pl_4Y0eZitmBnQ5jsBYZIBw', 'Updated payment link');

        /** @var PaymentLink */
        $paymentLink = $client->send($request);

        $this->assertTrue($paymentLink->getResponse()->successful());
        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new UpdatePaymentLinkRequest('pl_4Y0eZitmBnQ5jsBYZIBw', 'Updated payment link');

        $this->assertEquals('payment-links/pl_4Y0eZitmBnQ5jsBYZIBw', $request->resolveResourcePath());
    }
}
