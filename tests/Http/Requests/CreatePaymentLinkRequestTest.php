<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\CreatePaymentLinkPayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;
use Mollie\Api\Resources\PaymentLink;
use PHPUnit\Framework\TestCase;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;

class CreatePaymentLinkRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_payment_link()
    {
        $client = new MockMollieClient([
            CreatePaymentLinkRequest::class => new MockResponse(201, 'payment-link'),
        ]);

        $payload = new CreatePaymentLinkPayload(
            'Test payment link',
            new Money('EUR', '10.00')
        );

        $request = new CreatePaymentLinkRequest($payload);

        /** @var PaymentLink */
        $paymentLink = $client->send($request);

        $this->assertTrue($paymentLink->getResponse()->successful());
        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
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
