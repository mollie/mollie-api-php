<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\SessionCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedSessionsRequest extends ResourceHydratableRequest implements IsIteratable
{
    use IsIteratableRequest;

    protected $hydratableResource = SessionCollection::class;

    private ?string $from = null;

    private ?int $limit = null;

    private ?string $sort = null;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?string $sort = null
    ) {
        $this->from = $from;
        $this->limit = $limit;
        $this->sort = $sort;
    }

    protected function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
            'sort' => $this->sort,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'sessions';
    }
}
