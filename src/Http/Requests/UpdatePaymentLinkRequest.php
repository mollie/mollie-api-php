<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

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

    public function __construct(string $id, string $description, bool $archived = false, ?array $allowedMethods = null)
    {
        $this->id = $id;
        $this->description = $description;
        $this->archived = $archived;
        $this->allowedMethods = $allowedMethods;
    }

    protected function defaultPayload(): array
    {
        return [
            'description' => $this->description,
            'archived' => $this->archived,
            'allowedMethods' => $this->allowedMethods,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "payment-links/{$this->id}";
    }
}
