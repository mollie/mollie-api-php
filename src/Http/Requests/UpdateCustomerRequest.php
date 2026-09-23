<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/customers-api/update-customer
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Customer>
 */
class UpdateCustomerRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Customer::class;

    public function __construct(
        private string $id,
        private ?string $name = null,
        private ?string $email = null,
        private ?string $locale = null,
        private ?array $metadata = null,
    ) {
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
        return "customers/{$this->id}";
    }
}
