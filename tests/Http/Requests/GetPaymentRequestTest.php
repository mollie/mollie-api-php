<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\TestCase;

class GetPaymentRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment()
    {
        $client = MollieApiClient::fake([
            GetPaymentRequest::class => MockResponse::ok('payment'),
        ]);

        /** @var Payment */
        $payment = $client->send(new GetPaymentRequest('tr_WDqYK6vllg'));

        $this->assertTrue($payment->getResponse()->successful());
        $this->assertInstanceOf(Payment::class, $payment);

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $this->assertEmpty($pendingRequest->getUri()->getQuery());

            return true;
        });
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_WDqYK6vllg';
        $request = new GetPaymentRequest($paymentId);

        $this->assertEquals("payments/{$paymentId}", $request->resolveResourcePath());
    }
}
