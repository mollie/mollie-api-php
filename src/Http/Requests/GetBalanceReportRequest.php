<?php

namespace Mollie\Api\Http\Requests;

use DateTimeInterface;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
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

    private DateTimeInterface $from;

    private DateTimeInterface $until;

    private ?string $grouping;

    public function __construct(string $balanceId, DateTimeInterface $from, DateTimeInterface $until, ?string $grouping = null)
    {
        $this->balanceId = $balanceId;
        $this->from = $from;
        $this->until = $until;
        $this->grouping = $grouping;
    }

    protected function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'until' => $this->until,
            'grouping' => $this->grouping,
        ];
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "balances/{$this->balanceId}/report";
    }
}
