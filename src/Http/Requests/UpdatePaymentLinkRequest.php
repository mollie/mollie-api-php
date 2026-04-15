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
 * @see https://docs.mollie.com/reference/v2/payment-links-api/update-payment-link
 */
class UpdatePaymentLinkRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = PaymentLink::class;

    public function __construct(
        private string $id,
        private string $description,
        private bool $archived = false,
        private ?array $allowedMethods = null,
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
            'archived' => $this->archived,
            'allowedMethods' => $this->allowedMethods,
            'minimumAmount' => $this->minimumAmount,
            'lines' => $this->lines,
            'billingAddress' => $this->billingAddress,
            'shippingAddress' => $this->shippingAddress,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "payment-links/{$this->id}";
    }
}
