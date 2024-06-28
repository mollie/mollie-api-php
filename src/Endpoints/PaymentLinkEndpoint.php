<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\PaymentLinkCollection;

class PaymentLinkEndpoint extends EndpointCollection
{
    protected string $resourcePath = "payment-links";

    protected static string $resourceIdPrefix = 'pl_';

    /**
     * @inheritDoc
     */
    public static function getResourceClass(): string
    {
        return  PaymentLink::class;
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionClass(): string
    {
        return PaymentLinkCollection::class;
    }

    /**
     * Creates a payment link in Mollie.
     *
     * @param array $data An array containing details on the payment link.
     * @param array $filters
     *
     * @return PaymentLink
     * @throws ApiException
     */
    public function create(array $data = [], array $filters = []): PaymentLink
    {
        /** @var PaymentLink */
        return $this->createResource($data, $filters);
    }

    /**
     * Retrieve payment link from Mollie.
     *
     * Will throw a ApiException if the payment link id is invalid or the resource cannot be found.
     *
     * @param string $paymentLinkId
     * @param array $parameters
     * @return PaymentLink
     * @throws ApiException
     */
    public function get(string $paymentLinkId, array $parameters = []): PaymentLink
    {
        $this->guardAgainstInvalidId($paymentLinkId);

        /** @var PaymentLink */
        return $this->readResource($paymentLinkId, $parameters);
    }

    /**
     * Retrieves a collection of Payment Links from Mollie.
     *
     * @param string $from The first payment link ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return PaymentLinkCollection
     * @throws ApiException
     */
    public function page(string $from = null, int $limit = null, array $parameters = []): PaymentLinkCollection
    {
        /** @var PaymentLinkCollection */
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over payment links retrieved from Mollie.
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
