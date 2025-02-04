<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedPaymentLinkPaymentsRequest extends ResourceHydratableRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = PaymentCollection::class;

    private string $paymentLinkId;

    private ?string $from;

    private ?int $limit;

    private ?string $sort;

    public function __construct(string $paymentLinkId, ?string $from = null, ?int $limit = null, ?string $sort = null)
    {
        $this->paymentLinkId = $paymentLinkId;
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

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "payment-links/{$this->paymentLinkId}/payments";
    }
}
