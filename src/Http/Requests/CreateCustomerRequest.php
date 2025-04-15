<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreateCustomerRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    protected $hydratableResource = Customer::class;

    private ?string $name;

    private ?string $email;

    private ?string $locale;

    private ?array $metadata;

    public function __construct(
        ?string $name = null,
        ?string $email = null,
        ?string $locale = null,
        ?array $metadata = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->locale = $locale;
        $this->metadata = $metadata;
    }

    protected function defaultPayload(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'locale' => $this->locale,
            'metadata' => $this->metadata,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'customers';
    }
}
