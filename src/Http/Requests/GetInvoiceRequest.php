<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Invoice;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/invoices-api/get-invoice
 */
class GetInvoiceRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    protected ?string $hydratableResource = Invoice::class;

    public function __construct(
        private string $id,
    )
    {
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "invoices/{$this->id}";
    }
}
