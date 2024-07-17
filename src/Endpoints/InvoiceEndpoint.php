<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Invoice;
use Mollie\Api\Resources\InvoiceCollection;
use Mollie\Api\Resources\LazyCollection;

class InvoiceEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "invoices";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Invoice::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = InvoiceCollection::class;

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
        /** @var Invoice */
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
        /** @var InvoiceCollection */
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
