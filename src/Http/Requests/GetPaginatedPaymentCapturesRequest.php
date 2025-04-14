<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\PaymentIncludesQuery;
use Mollie\Api\Utils\Arr;

class GetPaginatedPaymentCapturesRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = CaptureCollection::class;

    private string $paymentId;

    public function __construct(string $paymentId, ?string $from = null, ?int $limit = null, bool $includePayment = false)
    {
        $this->paymentId = $paymentId;

        parent::__construct($from, $limit);

        $this->query()
            ->add('include', Arr::join($includePayment ? [PaymentIncludesQuery::PAYMENT] : []));
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/captures";
    }
}
