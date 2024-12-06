<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Query\GetPaymentQuery;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\Method;

class GetPaymentRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Payment::class;

    private string $id;

    private ?GetPaymentQuery $query = null;

    public function __construct(
        string $id,
        ?GetPaymentQuery $query = null
    ) {
        $this->id = $id;
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query ? $this->query->toArray() : [];
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "payments/{$this->id}";
    }
}
