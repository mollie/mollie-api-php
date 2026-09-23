<?php

declare(strict_types=1);

namespace Tests\Http\Data;

use Mollie\Api\Http\Data\PaymentStatusReason;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\ResourceHydrator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PaymentStatusReasonTest extends TestCase
{
    #[Test]
    public function it_round_trips_through_from_array_and_to_array(): void
    {
        $reason = PaymentStatusReason::fromArray([
            'code' => 'card_declined',
            'message' => 'The card was declined.',
        ]);

        $this->assertSame('card_declined', $reason->code);
        $this->assertSame('The card was declined.', $reason->message);
        $this->assertSame([
            'code' => 'card_declined',
            'message' => 'The card was declined.',
        ], $reason->toArray());
    }

    #[Test]
    public function payment_hydrates_a_status_reason_object(): void
    {
        $payment = $this->hydratePayment([
            'statusReason' => [
                'code' => 'some-future-code',
                'message' => 'Explained by Mollie.',
            ],
        ]);

        $this->assertInstanceOf(PaymentStatusReason::class, $payment->statusReason);
        $this->assertSame('some-future-code', $payment->statusReason->code);
        $this->assertSame('Explained by Mollie.', $payment->statusReason->message);
    }

    #[Test]
    public function payment_status_reason_is_null_when_null_or_omitted(): void
    {
        $this->assertNull($this->hydratePayment(['statusReason' => null])->statusReason);
        $this->assertNull($this->hydratePayment([])->statusReason);
    }

    /** @param array<string, mixed> $data */
    private function hydratePayment(array $data): Payment
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));
        (new ResourceHydrator)->hydrate(
            $payment,
            ['resource' => 'payment', 'id' => 'tr_x'] + $data,
            $this->createMock(Response::class),
        );

        return $payment;
    }
}
