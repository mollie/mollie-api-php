<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\CreateSalesInvoicePayloadFactory;
use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Factories\UpdateSalesInvoicePayloadFactory;
use Mollie\Api\Http\Data\CreateSalesInvoicePayload;
use Mollie\Api\Http\Data\UpdateSalesInvoicePayload;
use Mollie\Api\Http\Requests\CreateSalesInvoiceRequest;
use Mollie\Api\Http\Requests\DeleteSalesInvoiceRequest;
use Mollie\Api\Http\Requests\GetPaginatedSalesInvoicesRequest;
use Mollie\Api\Http\Requests\GetSalesInvoiceRequest;
use Mollie\Api\Http\Requests\UpdateSalesInvoiceRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Resources\SalesInvoiceCollection;

class SalesInvoiceEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a SalesInvoice from Mollie.
     *
     * @throws ApiException
     */
    public function get(string $id): SalesInvoice
    {
        return $this->send(new GetSalesInvoiceRequest($id));
    }

    /**
     * Creates a SalesInvoice in Mollie.
     *
     * @param  array|CreateSalesInvoicePayload  $payload
     *
     * @throws ApiException
     */
    public function create($payload = []): SalesInvoice
    {
        if (! $payload instanceof CreateSalesInvoicePayload) {
            $payload = CreateSalesInvoicePayloadFactory::new($payload)->create();
        }

        return $this->send(new CreateSalesInvoiceRequest($payload));
    }

    /**
     * Update a specific SalesInvoice resource.
     *
     * @throws ApiException
     */
    public function update(string $id, $payload = []): ?SalesInvoice
    {
        if (! $payload instanceof UpdateSalesInvoicePayload) {
            $payload = UpdateSalesInvoicePayloadFactory::new($payload)->create();
        }

        return $this->send(new UpdateSalesInvoiceRequest($id, $payload));
    }

    /**
     * Delete a SalesInvoice from Mollie.
     *
     * @throws ApiException
     */
    public function delete(string $id): void
    {
        $this->send(new DeleteSalesInvoiceRequest($id));
    }

    /**
     * Retrieves a collection of SalesInvoices from Mollie.
     *
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null): SalesInvoiceCollection
    {
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
        ])->create();

        return $this->send(new GetPaginatedSalesInvoicesRequest($query));
    }

    /**
     * Create an iterator for iterating over sales invoices retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        bool $iterateBackwards = false
    ): LazyCollection {
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
        ])->create();

        return $this->send(
            (new GetPaginatedSalesInvoicesRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
