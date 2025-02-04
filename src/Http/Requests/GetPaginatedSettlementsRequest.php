<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\SettlementCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedSettlementsRequest extends ResourceHydratableRequest implements IsIteratable
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = SettlementCollection::class;

    private ?string $from;

    private ?int $limit;

    private ?string $balanceId;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?string $balanceId = null
    ) {
        $this->from = $from;
        $this->limit = $limit;
        $this->balanceId = $balanceId;
    }

    protected function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
            'balanceId' => $this->balanceId,
        ];
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'settlements';
    }
}
