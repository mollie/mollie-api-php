<?php

declare(strict_types=1);

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\PaymentMethod;
use PHPUnit\Framework\TestCase;

class CreatePaymentRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_payment()
    {
        $client = new MockMollieClient([
            CreatePaymentRequest::class => MockResponse::created('payment'),
        ]);

        $request = new CreatePaymentRequest(
            'Test payment',
            new Money('EUR', '10.00'),
            'https://example.org/redirect',
            'https://example.org/webhook',
        );

        /** @var Payment */
        $payment = $client->send($request);

        $this->assertTrue($payment->getResponse()->successful());
        $this->assertInstanceOf(Payment::class, $payment);
    }

    /** @test */
    public function it_can_create_google_pay_payment_with_payment_token()
    {
        $client = MollieApiClient::fake([
            CreatePaymentRequest::class => MockResponse::created('payment'),
        ]);

        $client->payments->create([
            'method' => PaymentMethod::Creditcard,
            'googlePayPaymentToken' => '<stub_jwt>',
            'amount' => [
                'currency' => 'EUR',
                'value' => '10.00',
            ],
            'description' => 'Google Pay direct integration payment',
            'redirectUrl' => 'https://example.org/redirect',
        ]);

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $payload = json_decode((string) $pendingRequest->createPsrRequest()->getBody(), true);

            $this->assertSame('creditcard', $payload['method']);
            $this->assertSame('<stub_jwt>', $payload['googlePayPaymentToken']);

            return true;
        });
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreatePaymentRequest(
            'Test payment',
            new Money('EUR', '10.00'),
            'https://example.org/redirect',
            'https://example.org/webhook'
        );

        $this->assertEquals('payments', $request->resolveResourcePath());
    }
}
