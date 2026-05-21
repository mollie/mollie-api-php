<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/payment-links-api/create-payment-link
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\PaymentLink>
 */
class CreatePaymentLinkRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = PaymentLink::class;

    public function __construct(
        private string $description,
        private ?Money $amount = null,
        private ?string $redirectUrl = null,
        private ?string $webhookUrl = null,
        private ?string $profileId = null,
        private ?bool $reusable = null,
        private $expiresAt = null,
        private ?array $allowedMethods = null,
        private ?string $sequenceType = null,
        private ?string $customerId = null,
        private ?DataCollection $lines = null,
        private ?Address $billingAddress = null,
        private ?Address $shippingAddress = null,
        private ?Money $minimumAmount = null,
    ) {
    }

    protected function defaultPayload(): array
    {
        return [
            'description' => $this->description,
            'amount' => $this->amount,
            'minimumAmount' => $this->minimumAmount,
            'redirectUrl' => $this->redirectUrl,
            'webhookUrl' => $this->webhookUrl,
            'profileId' => $this->profileId,
            'reusable' => $this->reusable,
            'expiresAt' => $this->expiresAt,
            'allowedMethods' => $this->allowedMethods,
            'sequenceType' => $this->sequenceType,
            'customerId' => $this->customerId,
            'lines' => $this->lines,
            'billingAddress' => $this->billingAddress,
            'shippingAddress' => $this->shippingAddress,
        ];
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'payment-links';
    }
}
