<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\BalanceCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedBalanceRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected $hydratableResource = BalanceCollection::class;

    private ?string $from;

    private ?int $limit;

    private ?string $sort;

    public function __construct(?string $from = null, ?int $limit = null, ?string $sort = null)
    {
        $this->from = $from;
        $this->limit = $limit;
        $this->sort = $sort;
    }

    public function resolveResourcePath(): string
    {
        return 'balances';
    }

    protected function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
            'sort' => $this->sort,
        ];
    }
}
