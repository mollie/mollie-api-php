<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Types\Method;

class GetSalesInvoiceRequest extends ResourceHydratableRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = SalesInvoice::class;

    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function resolveResourcePath(): string
    {
        return "sales-invoices/{$this->id}";
    }
}
