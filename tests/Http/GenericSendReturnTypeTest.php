<?php

declare(strict_types=1);

namespace Tests\Http;

use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\PaymentStatus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Guards the generic `send()` return-type contract (issue #875).
 *
 * The runtime assertions below mirror what PHPStan validates statically:
 * `$client->send($request)` must be accepted where a `Payment` is required.
 * If the hydrator or the generic annotations drift, this test fails.
 */
class GenericSendReturnTypeTest extends TestCase
{
    #[Test]
    public function send_returns_typed_payment_resource_for_get_payment_request(): void
    {
        $client = MollieApiClient::fake([
            GetPaymentRequest::class => MockResponse::ok('payment'),
        ]);

        // No @var annotation, no cast — PHPStan resolves Payment via the generic.
        $payment = $client->send(new GetPaymentRequest('tr_WDqYK6vllg'));

        $this->assertPayment(self::acceptPayment($payment));
    }

    #[Test]
    public function send_returns_typed_payment_resource_for_create_payment_request(): void
    {
        $client = MollieApiClient::fake([
            CreatePaymentRequest::class => MockResponse::ok('payment'),
        ]);

        $createPaymentRequest = new CreatePaymentRequest(
            description: 'Test payment',
            amount: Money::euro('10.00'),
            redirectUrl: 'https://example.org/redirect',
            webhookUrl: 'https://example.org/webhook',
            locale: 'en_US',
            metadata: ['order_id' => '123'],
        );

        // No @var annotation, no cast — PHPStan resolves Payment via the generic.
        $payment = $client->send($createPaymentRequest);

        $this->assertPayment(self::acceptPayment($payment));
    }

    private static function acceptPayment(Payment $payment): Payment
    {
        return $payment;
    }

    private function assertPayment(Payment $payment): void
    {
        $this->assertNotEmpty($payment->id);
        $this->assertInstanceOf(Money::class, $payment->amount);

        // Status is the backed enum after hydration (or raw string for unknown values).
        $this->assertTrue(
            $payment->status instanceof PaymentStatus || is_string($payment->status),
            'status must be either a PaymentStatus case or a raw string'
        );
    }
}
