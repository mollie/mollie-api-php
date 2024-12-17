<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Data\GetPaginatedPaymentCapturesQuery;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedPaymentCapturesRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = CaptureCollection::class;

    private string $paymentId;

    private ?GetPaginatedPaymentCapturesQuery $query;

    public function __construct(string $paymentId, ?GetPaginatedPaymentCapturesQuery $query = null)
    {
        $this->paymentId = $paymentId;
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query ? $this->query->toArray() : [];
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/captures";
    }
}
