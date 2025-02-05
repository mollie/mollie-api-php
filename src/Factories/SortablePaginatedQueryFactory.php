<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\SortablePaginatedQuery;

class SortablePaginatedQueryFactory extends RequestFactory
{
    public static function new(...$args): static
    {
        return new static(...$args);
    }

    public function create(): SortablePaginatedQuery
    {
        return new SortablePaginatedQuery(
            $this->query('from'),
            $this->query('limit'),
            $this->query('sort'),
        );
    }
}
