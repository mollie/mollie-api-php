<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Invoice;
use Mollie\Api\Types\Method;

class GetInvoiceRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    protected $hydratableResource = Invoice::class;

    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "invoices/{$this->id}";
    }
}
