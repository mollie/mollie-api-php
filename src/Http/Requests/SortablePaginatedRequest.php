<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Query\SortablePaginatedQuery;

abstract class SortablePaginatedRequest extends PaginatedRequest
{
    public function __construct(
        ?SortablePaginatedQuery $query = null
    ) {
        parent::__construct($query);
    }
}
