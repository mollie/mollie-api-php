<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Data\GetBalanceReportQuery;
use Mollie\Api\Resources\BalanceReport;
use Mollie\Api\Types\Method;

class GetBalanceReportRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = BalanceReport::class;

    private string $balanceId;

    private GetBalanceReportQuery $query;

    public function __construct(string $balanceId, GetBalanceReportQuery $query)
    {
        $this->balanceId = $balanceId;
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query->toArray();
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "balances/{$this->balanceId}/report";
    }
}
