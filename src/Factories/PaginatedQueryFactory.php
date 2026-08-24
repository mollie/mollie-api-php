<?php

declare(strict_types=1);

namespace Mollie\Api\Factories;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Http\Data\PaginatedQuery;

class PaginatedQueryFactory extends RequestFactory
{
    /**
     * Seed the query map. A later withQuery() call replaces this input.
     *
     * @param  array<string, mixed>|Arrayable|null  $data
     */
    public function __construct($data = null)
    {
        parent::__construct();

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $this->withQuery($data ?: []);
    }

    public function create(): PaginatedQuery
    {
        return new PaginatedQuery(
            $this->query('from'),
            $this->query('limit'),
        );
    }
}
