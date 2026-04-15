<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/get-sales-invoice
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\SalesInvoice>
 */
class GetSalesInvoiceRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = SalesInvoice::class;

    public function __construct(
        private string $id,
    )
    {
    }

    public function resolveResourcePath(): string
    {
        return "sales-invoices/{$this->id}";
    }
}
