<?php

declare(strict_types=1);

namespace Mollie\Api\Factories;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Http\Data\SortablePaginatedQuery;

class SortablePaginatedQueryFactory extends RequestFactory
{
    /**
     * Seed the query map. A later withQuery() call replaces this input.
     *
     * @param  array<string, mixed>|Arrayable|null  $data
     */
    public function __construct($data = null)
    {
        parent::__construct($data);

        $this->withQuery($this->get());
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
