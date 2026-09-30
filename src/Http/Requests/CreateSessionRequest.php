<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\OrderLine;
use Mollie\Api\Resources\Session;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreateSessionRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    protected $hydratableResource = Session::class;

    private Money $amount;

    private string $description;

    private string $redirectUrl;

    /**
     * @var DataCollection<OrderLine>
     */
    private DataCollection $lines;

    private ?string $cancelUrl;

    private ?Address $billingAddress;

    private ?Address $shippingAddress;

    private ?string $customerId;

    private ?string $sequenceType;

    private ?array $metadata;

    private ?string $paymentWebhook;

    private ?string $profileId;

    /**
     * @var array<string>|null
     */
    private ?array $requiredCustomerDetails;

    /**
     * @param  string|null  $cancelUrl  Deprecated: not part of the Checkout Sessions API; kept for backwards compatibility.
     * @param  array<string>|null  $requiredCustomerDetails  Any of "email", "billing-address", "shipping-address" (private beta).
     */
    public function __construct(
        Money $amount,
        string $description,
        string $redirectUrl,
        DataCollection $lines,
        ?string $cancelUrl = null,
        ?Address $billingAddress = null,
        ?Address $shippingAddress = null,
        ?string $customerId = null,
        ?string $sequenceType = null,
        ?array $metadata = null,
        ?string $paymentWebhook = null,
        ?string $profileId = null,
        ?array $requiredCustomerDetails = null
    ) {
        $this->amount = $amount;
        $this->description = $description;
        $this->redirectUrl = $redirectUrl;
        $this->lines = $lines;
        $this->cancelUrl = $cancelUrl;
        $this->billingAddress = $billingAddress;
        $this->shippingAddress = $shippingAddress;
        $this->customerId = $customerId;
        $this->sequenceType = $sequenceType;
        $this->metadata = $metadata;
        $this->paymentWebhook = $paymentWebhook;
        $this->profileId = $profileId;
        $this->requiredCustomerDetails = $requiredCustomerDetails;
    }

    protected function defaultPayload(): array
    {
        return [
            'amount' => $this->amount,
            'description' => $this->description,
            'redirectUrl' => $this->redirectUrl,
            'lines' => $this->lines,
            'cancelUrl' => $this->cancelUrl,
            'billingAddress' => $this->billingAddress,
            'shippingAddress' => $this->shippingAddress,
            'customerId' => $this->customerId,
            'sequenceType' => $this->sequenceType,
            'metadata' => $this->metadata,
            'profileId' => $this->profileId,
            'requiredCustomerDetails' => $this->requiredCustomerDetails,
            'payment' => $this->paymentWebhook !== null ? [
                'webhookUrl' => $this->paymentWebhook,
            ] : null,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'sessions';
    }
}
