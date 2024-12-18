<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\SalesInvoiceCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedSalesInvoicesRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected $hydratableResource = SalesInvoiceCollection::class;

    public function resolveResourcePath(): string
    {
        return 'sales-invoices';
    }
}
