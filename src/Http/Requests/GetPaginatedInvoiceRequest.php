<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\InvoiceCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedInvoiceRequest extends ResourceHydratableRequest implements IsIteratable
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = InvoiceCollection::class;

    private ?string $from;

    private ?int $limit;

    private ?string $reference;

    private ?string $year;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?string $reference = null,
        ?string $year = null
    ) {
        $this->from = $from;
        $this->limit = $limit;
        $this->reference = $reference;
        $this->year = $year;
    }

    public function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
            'reference' => $this->reference,
            'year' => $this->year,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'invoices';
    }
}
