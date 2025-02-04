<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\BalanceTransactionCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedBalanceTransactionRequest extends ResourceHydratableRequest implements IsIteratable
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = BalanceTransactionCollection::class;

    private string $balanceId;

    private ?string $from;

    private ?int $limit;

    public function __construct(
        string $balanceId,
        ?string $from = null,
        ?int $limit = null,
    ) {
        $this->balanceId = $balanceId;
        $this->from = $from;
        $this->limit = $limit;
    }

    protected function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "balances/{$this->balanceId}/transactions";
    }
}
