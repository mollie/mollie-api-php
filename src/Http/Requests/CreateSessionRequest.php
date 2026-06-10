<?php

declare(strict_types=1);

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

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Session>
 */
class CreateSessionRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    protected ?string $hydratableResource = Session::class;

    /**
     * @param DataCollection<OrderLine> $lines
     */
    public function __construct(
        private Money $amount,
        private string $description,
        private string $redirectUrl,
        private DataCollection $lines,
        private ?string $cancelUrl = null,
        private ?Address $billingAddress = null,
        private ?Address $shippingAddress = null,
        private ?string $customerId = null,
        private ?string $sequenceType = null,
        private ?array $metadata = null,
        private ?string $paymentWebhook = null,
        private ?string $profileId = null,
    ) {
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
