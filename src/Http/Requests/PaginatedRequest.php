<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Query\Query;
use Mollie\Api\Types\Method;

abstract class PaginatedRequest extends ResourceHydratableRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    private ?Query $query = null;

    public function __construct(
        ?Query $query = null
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
