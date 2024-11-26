<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\GetPaginatedInvoiceQueryFactory;
use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Http\Requests\GetInvoiceRequest;
use Mollie\Api\Http\Requests\GetPaginatedInvoiceRequest;
use Mollie\Api\Resources\Invoice;
use Mollie\Api\Resources\InvoiceCollection;
use Mollie\Api\Resources\LazyCollection;

class InvoiceEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve an Invoice from Mollie.
     *
     * Will throw a ApiException if the invoice id is invalid or the resource cannot be found.
     *
     * @throws ApiException
     */
    public function get(string $invoiceId): Invoice
    {
        /** @var Invoice */
        return $this->send(new GetInvoiceRequest($invoiceId));
    }

    /**
     * Retrieves a collection of Invoices from Mollie.
     *
     * @param  string|null  $from  The first invoice ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): InvoiceCollection
    {
        $query = GetPaginatedInvoiceQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        /** @var InvoiceCollection */
        return $this->send(new GetPaginatedInvoiceRequest($query));
    }

    /**
     * Create an iterator for iterating over invoices retrieved from Mollie.
     *
     * @param  string|null  $from  The first invoice ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(?string $from = null, ?int $limit = null, array $parameters = [], bool $iterateBackwards = false): LazyCollection
    {
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $parameters,
        ])->create();

        return $this->send(
            (new GetPaginatedInvoiceRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
