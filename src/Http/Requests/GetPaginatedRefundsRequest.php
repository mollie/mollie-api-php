<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedRefundsRequest extends ResourceHydratableRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = RefundCollection::class;

    private ?string $from;

    private ?int $limit;

    private bool $embedPayment;

    private ?string $profileId;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        bool $embedPayment = false,
        ?string $profileId = null
    ) {
        $this->from = $from;
        $this->limit = $limit;
        $this->embedPayment = $embedPayment;
        $this->profileId = $profileId;
    }

    protected function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
            'embed' => $this->embedPayment ? PaymentIncludesQuery::PAYMENT : null,
            'profileId' => $this->profileId,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'refunds';
    }
}
