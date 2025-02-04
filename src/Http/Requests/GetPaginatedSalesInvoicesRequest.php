<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\SalesInvoiceCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedSalesInvoicesRequest extends ResourceHydratableRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    protected $hydratableResource = SalesInvoiceCollection::class;

    private ?string $from;

    private ?int $limit;

    public function __construct(
        ?string $from = null,
        ?int $limit = null
    ) {
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
        return 'sales-invoices';
    }
}
