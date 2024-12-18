<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\TestCase;

class GetPaymentRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment()
    {
        $client = new MockMollieClient([
            GetPaymentRequest::class => new MockResponse(200, 'payment'),
        ]);

        $paymentId = 'tr_WDqYK6vllg';
        $request = new GetPaymentRequest($paymentId);

        /** @var Payment */
        $payment = $client->send($request);

        $this->assertTrue($payment->getResponse()->successful());
        $this->assertInstanceOf(Payment::class, $payment);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_WDqYK6vllg';
        $request = new GetPaymentRequest($paymentId);

        $this->assertEquals("payments/{$paymentId}", $request->resolveResourcePath());
    }
}
