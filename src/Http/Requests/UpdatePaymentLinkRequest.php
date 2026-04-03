<?php

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
    protected $hydratableResource = PaymentLink::class;

    private string $id;

    private string $description;

    private bool $archived;

    private ?array $allowedMethods;

    private ?DataCollection $lines;

    private ?Address $billingAddress;

    private ?Address $shippingAddress;

    private ?Money $minimumAmount;

    public function __construct(
        string $id,
        string $description,
        bool $archived = false,
        ?array $allowedMethods = null,
        ?DataCollection $lines = null,
        ?Address $billingAddress = null,
        ?Address $shippingAddress = null,
        ?Money $minimumAmount = null
    ) {
        $this->id = $id;
        $this->description = $description;
        $this->archived = $archived;
        $this->allowedMethods = $allowedMethods;
        $this->lines = $lines;
        $this->billingAddress = $billingAddress;
        $this->shippingAddress = $shippingAddress;
        $this->minimumAmount = $minimumAmount;
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
