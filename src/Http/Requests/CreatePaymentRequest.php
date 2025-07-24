<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\ApplicationFee;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\OrderLine;
use Mollie\Api\Http\Data\PaymentRoute;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\PaymentQuery;

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
    protected $hydratableResource = Payment::class;

    private string $description;

    private Money $amount;

    private ?string $redirectUrl;

    private ?string $cancelUrl;

    private ?string $webhookUrl;

    /**
     * @var DataCollection<OrderLine>|null
     */
    private ?DataCollection $lines;

    private ?Address $billingAddress;

    private ?Address $shippingAddress;

    private ?string $locale;

    /**
     * @var array|string|null
     */
    private $paymentMethod;

    private ?string $issuer;

    private ?string $restrictPaymentMethodsToCountry;

    private ?array $metadata;

    private ?string $captureMode;

    private ?string $captureDelay;

    private ?ApplicationFee $applicationFee;

    /**
     * @var DataCollection<PaymentRoute>|null
     */
    private ?DataCollection $routing;

    private ?string $sequenceType;

    private ?string $mandateId;

    private ?string $customerId;

    private ?string $profileId;

    /**
     * Method specific data.
     *
     * s. https://docs.mollie.com/reference/extra-payment-parameters#payment-creation-request-parameters
     */
    private array $additional = [];

    private bool $includeQrCode;

    /**
     * @param  array|string|null  $method
     */
    public function __construct(
        string $description,
        Money $amount,
        ?string $redirectUrl = null,
        ?string $cancelUrl = null,
        ?string $webhookUrl = null,
        ?DataCollection $lines = null,
        ?Address $billingAddress = null,
        ?Address $shippingAddress = null,
        ?string $locale = null,
        $method = null,
        ?string $issuer = null,
        ?string $restrictPaymentMethodsToCountry = null,
        ?array $metadata = null,
        ?string $captureMode = null,
        ?string $captureDelay = null,
        ?ApplicationFee $applicationFee = null,
        ?DataCollection $routing = null,
        ?string $sequenceType = null,
        ?string $mandateId = null,
        ?string $customerId = null,
        ?string $profileId = null,
        array $additional = [],
        bool $includeQrCode = false
    ) {
        $this->description = $description;
        $this->amount = $amount;
        $this->redirectUrl = $redirectUrl;
        $this->cancelUrl = $cancelUrl;
        $this->webhookUrl = $webhookUrl;
        $this->lines = $lines;
        $this->billingAddress = $billingAddress;
        $this->shippingAddress = $shippingAddress;
        $this->locale = $locale;
        $this->paymentMethod = $method;
        $this->issuer = $issuer;
        $this->restrictPaymentMethodsToCountry = $restrictPaymentMethodsToCountry;
        $this->metadata = $metadata;
        $this->captureMode = $captureMode;
        $this->captureDelay = $captureDelay;
        $this->applicationFee = $applicationFee;
        $this->routing = $routing;
        $this->sequenceType = $sequenceType;
        $this->mandateId = $mandateId;
        $this->customerId = $customerId;
        $this->profileId = $profileId;
        $this->additional = $additional;
        $this->includeQrCode = $includeQrCode;
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
