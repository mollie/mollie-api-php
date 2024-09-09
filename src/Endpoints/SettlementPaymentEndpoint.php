<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;

class SettlementPaymentEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "settlements_payments";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Payment::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = PaymentCollection::class;

    /**
     * Retrieves a collection of Payments from Mollie.
     *
     * @param string $settlementId
     * @param string $from The first payment ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return PaymentCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pageForId($settlementId, ?string $from = null, ?int $limit = null, array $parameters = []): PaymentCollection
    {
        $this->parentId = $settlementId;

        /** @var PaymentCollection */
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over payments for the given settlement id, retrieved from Mollie.
     *
     * @param string $settlementId
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iteratorForId(
        string $settlementId,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $this->parentId = $settlementId;

        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
