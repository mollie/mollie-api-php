<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Payload\CreateSalesInvoicePayload;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreateSalesInvoiceRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;
    public static string $targetResourceClass = SalesInvoice::class;

    private CreateSalesInvoicePayload $payload;

    public function __construct(CreateSalesInvoicePayload $payload)
    {
        $this->payload = $payload;
    }

    public function resolveResourcePath(): string
    {
        return 'sales-invoices';
    }

    public function defaultPayload(): array
    {
        return $this->payload->toArray();
    }
}
