<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Invoice;
use Mollie\Api\Resources\InvoiceCollection;
use Mollie\Api\Resources\LazyCollection;

class InvoiceEndpoint extends EndpointCollection
{
    protected string $resourcePath = "invoices";

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Invoice
    {
        return new Invoice($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): InvoiceCollection
    {
        return new InvoiceCollection($this->client, $count, $_links);
    }

    /**
     * Retrieve an Invoice from Mollie.
     *
     * Will throw a ApiException if the invoice id is invalid or the resource cannot be found.
     *
     * @param string $invoiceId
     * @param array $parameters
     *
     * @return Invoice
     * @throws ApiException
     */
    public function get(string $invoiceId, array $parameters = []): Invoice
    {
        return $this->readResource($invoiceId, $parameters);
    }

    /**
     * Retrieves a collection of Invoices from Mollie.
     *
     * @param string $from The first invoice ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return InvoiceCollection
     * @throws ApiException
     */
    public function page(string $from = null, int $limit = null, array $parameters = []): InvoiceCollection
    {
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * This is a wrapper method for page
     *
     * @param array $parameters
     *
     * @return InvoiceCollection
     * @throws ApiException
     */
    public function all(array $parameters = []): InvoiceCollection
    {
        return $this->page(null, null, $parameters);
    }

    /**
     * Create an iterator for iterating over invoices retrieved from Mollie.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iterator(?string $from = null, ?int $limit = null, array $parameters = [], bool $iterateBackwards = false): LazyCollection
    {
        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
