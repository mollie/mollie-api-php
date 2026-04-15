<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/customers-api/get-customer
 */
class GetCustomerRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::GET;

    protected ?string $hydratableResource = Customer::class;

    public function __construct(
        private string $id,
    )
    {
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "customers/{$this->id}";
    }
}
