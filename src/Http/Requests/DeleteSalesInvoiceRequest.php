<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Types\Method;

class DeleteSalesInvoiceRequest extends Request
{
    protected static string $method = Method::DELETE;

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
