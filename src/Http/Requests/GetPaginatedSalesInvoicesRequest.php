<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\SalesInvoiceCollection;
use Mollie\Api\Traits\IsIteratableRequest;

/**
 * @extends PaginatedRequest<\Mollie\Api\Resources\SalesInvoiceCollection>
 */
class GetPaginatedSalesInvoicesRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected ?string $hydratableResource = SalesInvoiceCollection::class;

    public function resolveResourcePath(): string
    {
        return 'sales-invoices';
    }
}
