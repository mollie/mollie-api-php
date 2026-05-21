<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\ApplicationFee;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\PaymentQuery;

/**
 * @see https://docs.mollie.com/reference/v2/payments-api/create-payment
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Payment>
 */
class CreatePaymentRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Payment::class;

    private string|array|null $paymentMethod;

    public function __construct(
        private string $description,
        private Money $amount,
        private ?string $redirectUrl = null,
        private ?string $cancelUrl = null,
        private ?string $webhookUrl = null,
        private ?DataCollection $lines = null,
        private ?Address $billingAddress = null,
        private ?Address $shippingAddress = null,
        private ?string $locale = null,
        string|array|null $method = null,
        private ?string $issuer = null,
        private ?string $restrictPaymentMethodsToCountry = null,
        private ?array $metadata = null,
        private ?string $captureMode = null,
        private ?string $captureDelay = null,
        private ?ApplicationFee $applicationFee = null,
        private ?DataCollection $routing = null,
        private ?string $sequenceType = null,
        private ?string $mandateId = null,
        private ?string $customerId = null,
        private ?string $profileId = null,
        private array $additional = [],
        private bool $includeQrCode = false,
    ) {
        $this->paymentMethod = $method;
    }

    protected function defaultPayload(): array
    {
        return array_merge([
            'description' => $this->description,
            'amount' => $this->amount,
            'redirectUrl' => $this->redirectUrl,
            'cancelUrl' => $this->cancelUrl,
            'webhookUrl' => $this->webhookUrl,
            'lines' => $this->lines,
            'billingAddress' => $this->billingAddress,
            'shippingAddress' => $this->shippingAddress,
            'locale' => $this->locale,
            'method' => $this->paymentMethod,
            'issuer' => $this->issuer,
            'restrictPaymentMethodsToCountry' => $this->restrictPaymentMethodsToCountry,
            'metadata' => $this->metadata,
            'captureMode' => $this->captureMode,
            'captureDelay' => $this->captureDelay,
            'applicationFee' => $this->applicationFee,
            'routing' => $this->routing,
            'sequenceType' => $this->sequenceType,
            'mandateId' => $this->mandateId,
            'customerId' => $this->customerId,
            'profileId' => $this->profileId,
        ], $this->additional);
    }

    protected function defaultQuery(): array
    {
        return [
            'include' => $this->includeQrCode ? PaymentQuery::INCLUDE_QR_CODE : null,
        ];
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'payments';
    }
}
