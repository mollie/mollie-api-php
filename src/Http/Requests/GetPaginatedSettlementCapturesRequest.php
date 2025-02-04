<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\PaymentIncludesQuery;
use Mollie\Api\Utils\Arr;

class GetPaginatedSettlementCapturesRequest extends ResourceHydratableRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = CaptureCollection::class;

    private string $settlementId;

    private ?string $from = null;

    private ?int $limit = null;

    private bool $includePayment = false;

    public function __construct(
        string $settlementId,
        ?string $from = null,
        ?int $limit = null,
        bool $includePayment = false
    ) {
        $this->settlementId = $settlementId;
        $this->from = $from;
        $this->limit = $limit;
        $this->includePayment = $includePayment;
    }

    protected function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
            'include' => Arr::join($this->includePayment ? [PaymentIncludesQuery::PAYMENT] : []),
        ];
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "settlements/{$this->settlementId}/captures";
    }
}
