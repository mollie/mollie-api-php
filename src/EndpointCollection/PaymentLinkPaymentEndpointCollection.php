<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\SortablePaginatedQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Requests\GetPaginatedPaymentLinkPaymentsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\PaymentLink;

class PaymentLinkPaymentEndpointCollection extends EndpointCollection
{
    /**
     * Retrieves a collection of Payments from Mollie for the given Payment Link.
     *
     * @param  string|null  $from  The first payment ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function pageFor(PaymentLink $paymentLink, ?string $from = null, ?int $limit = null, ?array $filters = null): PaymentCollection
    {
        return $this->pageForId($paymentLink->id, $from, $limit, $filters);
    }

    /**
     * Retrieves a collection of Payments from Mollie for the given Payment Link ID.
     *
     * @param  string|null  $from  The first payment ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function pageForId(string $paymentLinkId, ?string $from = null, ?int $limit = null, ?array $filters = null): PaymentCollection
    {
        $testmode = Helpers::extractBool($filters, 'testmode', false);
        $query = SortablePaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        /** @var PaymentCollection */
        return $this->send((new GetPaginatedPaymentLinkPaymentsRequest($paymentLinkId, $query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over payments associated with the provided Payment Link object, retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorFor(
        PaymentLink $paymentLink,
        ?string $from = null,
        ?int $limit = null,
        ?array $filters = null,
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->iteratorForId(
            $paymentLink->id,
            $from,
            $limit,
            $filters,
            $iterateBackwards
        );
    }

    /**
     * Create an iterator for iterating over payments associated with the provided Payment Link id, retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorForId(
        string $paymentLinkId,
        ?string $from = null,
        ?int $limit = null,
        ?array $filters = null,
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Helpers::extractBool($filters, 'testmode', false);
        $query = SortablePaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedPaymentLinkPaymentsRequest($paymentLinkId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
