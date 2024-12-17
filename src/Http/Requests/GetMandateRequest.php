<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Types\Method;

class GetMandateRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Mandate::class;

    private string $customerId;

    private string $mandateId;

    public function __construct(string $customerId, string $mandateId)
    {
        $this->customerId = $customerId;
        $this->mandateId = $mandateId;
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/mandates/{$this->mandateId}";
    }
}
