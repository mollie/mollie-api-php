<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Query\PaginatedQuery;
use Mollie\Api\Http\Request;
use Mollie\Api\Types\Method;

abstract class PaginatedRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    private ?PaginatedQuery $query = null;

    public function __construct(
        ?PaginatedQuery $query = null
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
