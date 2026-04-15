<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use DateTimeInterface;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\BalanceReport;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\BalanceReport>
 */
class GetBalanceReportRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = BalanceReport::class;

    public function __construct(
        private string $balanceId,
        private DateTimeInterface $from,
        private DateTimeInterface $until,
        private ?string $grouping = null,
    )
    {
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
