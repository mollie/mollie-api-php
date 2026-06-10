<?php

declare(strict_types=1);

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

    /** @test */
    public function it_encodes_resource_ids_when_creating_the_psr_request()
    {
        $client = MollieApiClient::fake([
            GetPaymentRequest::class => MockResponse::ok('payment'),
        ]);

        $client->send(new GetPaymentRequest('tr_x%2F..%2Forders'));

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $this->assertSame(
                'https://api.mollie.com/v2/payments/tr_x%252F..%252Forders',
                (string) $pendingRequest->getUri()
            );

            return true;
        });
    }
}
