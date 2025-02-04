<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\InvoiceCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedInvoiceRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = InvoiceCollection::class;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?string $reference = null,
        ?string $year = null
    ) {
        parent::__construct($from, $limit);

        $this->query()
            ->add('reference', $reference)
            ->add('year', $year);
    }

    public function resolveResourcePath(): string
    {
        return 'invoices';
    }
}
