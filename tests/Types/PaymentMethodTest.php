<?php

declare(strict_types=1);

namespace Tests\Types;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\ResourceHydrator;
use Mollie\Api\Types\PaymentMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PaymentMethodTest extends TestCase
{
    /**
     * Values present in the create-payment request and get-payment response
     * enums of mollie/openapi@cfbba47b874b90ea54dc5e2643d163b8bc527902
     * (2026-08-26) that beta.2 lacked.
     */
    public static function dpMethodsAddedFromTheSpec(): array
    {
        return [
            ['billink', PaymentMethod::Billink],
            ['bizum', PaymentMethod::Bizum],
            ['mobilepay', PaymentMethod::Mobilepay],
            ['vipps', PaymentMethod::Vipps],
            ['voucher', PaymentMethod::Voucher],
        ];
    }

    #[DataProvider('dpMethodsAddedFromTheSpec')]
    #[Test]
    public function it_exposes_the_methods_the_api_accepts(string $value, PaymentMethod $case): void
    {
        $this->assertSame($case, PaymentMethod::from($value));
    }

    #[DataProvider('dpMethodsAddedFromTheSpec')]
    #[Test]
    public function the_new_cases_serialize_into_a_create_payment_request(string $value, PaymentMethod $case): void
    {
        $client = new MockMollieClient([CreatePaymentRequest::class => MockResponse::ok('payment')]);

        $client->send(new CreatePaymentRequest(description: 'Order', amount: Money::euro('10.00'), method: $case));

        $client->assertSent(function (PendingRequest $pendingRequest) use ($value) {
            $payload = json_decode((string) $pendingRequest->createPsrRequest()->getBody(), true);

            $this->assertSame($value, $payload['method']);

            return true;
        });
    }

    #[Test]
    public function a_method_unknown_to_the_sdk_hydrates_as_a_raw_string(): void
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));
        (new ResourceHydrator)->hydrate($payment, ['resource' => 'payment', 'id' => 'tr_x', 'method' => 'method-from-the-future'], $this->createMock(Response::class));

        $this->assertSame('method-from-the-future', $payment->method);
    }

    #[Test]
    public function existing_cases_are_kept(): void
    {
        foreach (['bitcoin', 'giropay', 'googlepay', 'inghomepay', 'podiumcadeaukaart', 'sofort'] as $legacy) {
            $this->assertInstanceOf(PaymentMethod::class, PaymentMethod::from($legacy));
        }
    }
}
