<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Payload\UpdateSalesInvoicePayload;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class UpdateSalesInvoiceRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;
    public static string $targetResourceClass = SalesInvoice::class;

    private string $id;
    private UpdateSalesInvoicePayload $payload;

    public function __construct(string $id, UpdateSalesInvoicePayload $payload)
    {
        $this->id = $id;
        $this->payload = $payload;
    }

    public function resolveResourcePath(): string
    {
        return "sales-invoices/{$this->id}";
    }

    public function defaultPayload(): array
    {
        return $this->payload->toArray();
    }
}
