<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Rules\Included;

class SortablePaginatedQuery extends PaginatedQuery
{
    public ?string $sort = null;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?string $sort = null,
        ?bool $testmode = null
    ) {
        parent::__construct($from, $limit, $testmode);

        $this->sort = $sort;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'sort' => $this->sort,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'sort' => Included::in(['asc', 'desc']),
        ];
    }
}
