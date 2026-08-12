<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

/**
 * @template TResource of object
 *
 * @extends PaginatedRequest<TResource>
 */
abstract class SortablePaginatedRequest extends PaginatedRequest
{
    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?string $sort = null
    ) {
        parent::__construct($from, $limit);

        $this->query()->add('sort', $sort);
    }
}
