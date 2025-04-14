<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CancelPaymentRequest;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\TestCase;

class CancelPaymentRequestTest extends TestCase
{
    /** @test */
    public function it_can_cancel_payment()
    {
        $client = new MockMollieClient([
            CancelPaymentRequest::class => MockResponse::ok('payment'),
        ]);

        $paymentId = 'tr_WDqYK6vllg';
        $request = new CancelPaymentRequest($paymentId);

        /** @var Payment */
        $payment = $client->send($request);

        $this->assertTrue($payment->getResponse()->successful());
        $this->assertInstanceOf(Payment::class, $payment);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_WDqYK6vllg';
        $request = new CancelPaymentRequest($paymentId);

        $this->assertEquals(
            "payments/{$paymentId}",
            $request->resolveResourcePath()
        );
    }
}
