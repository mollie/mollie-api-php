<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Types\Method;

abstract class PaginatedRequest extends ResourceHydratableRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    private ?Arrayable $query = null;

    public function __construct(
        ?Arrayable $query = null
    ) {
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query
            ? $this->query->toArray()
            : [];
    }
}
