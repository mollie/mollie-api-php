<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\GetPaginatedInvoiceRequestFactory;
use Mollie\Api\Http\Requests\GetInvoiceRequest;
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
     * @throws RequestException
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
     * @throws RequestException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): InvoiceCollection
    {
        $request = GetPaginatedInvoiceRequestFactory::new()
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        /** @var InvoiceCollection */
        return $this->send($request);
    }

    /**
     * Create an iterator for iterating over invoices retrieved from Mollie.
     *
     * @param  string|null  $from  The first invoice ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        $request = GetPaginatedInvoiceRequestFactory::new()
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        return $this->send(
            $request
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
