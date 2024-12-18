<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\CreatePaymentPayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\TestCase;

class CreatePaymentRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_payment()
    {
        $client = new MockMollieClient([
            CreatePaymentRequest::class => new MockResponse(201, 'payment'),
        ]);

        $payload = new CreatePaymentPayload(
            'Test payment',
            new Money('EUR', '10.00'),
            'https://example.org/redirect',
            'https://example.org/webhook'
        );

        $request = new CreatePaymentRequest($payload);

        /** @var Payment */
        $payment = $client->send($request);

        $this->assertTrue($payment->getResponse()->successful());
        $this->assertInstanceOf(Payment::class, $payment);
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
