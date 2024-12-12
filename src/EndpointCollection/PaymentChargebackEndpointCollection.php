<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\GetPaginatedPaymentChargebacksQueryFactory;
use Mollie\Api\Factories\GetPaymentChargebackQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Data\GetPaginatedPaymentChargebacksQuery;
use Mollie\Api\Http\Data\GetPaymentChargebackQuery;
use Mollie\Api\Http\Requests\GetPaginatedPaymentChargebacksRequest;
use Mollie\Api\Http\Requests\GetPaymentChargebackRequest;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;

class PaymentChargebackEndpointCollection extends EndpointCollection
{
    /**
     * @param  array|GetPaymentChargebackQuery  $query
     *
     * @throws ApiException
     */
    public function getFor(Payment $payment, string $chargebackId, $query = [], ?bool $testmode = null): Chargeback
    {
        return $this->getForId($payment->id, $chargebackId, $query, $testmode);
    }

    /**
     * @param  array|GetPaymentChargebackQuery  $query
     *
     * @throws ApiException
     */
    public function getForId(string $paymentId, string $chargebackId, $query = [], bool $testmode = false): Chargeback
    {
        if (! $query instanceof GetPaymentChargebackQuery) {
            $testmode = Helpers::extractBool($query, 'testmode', $testmode);
            $query = GetPaymentChargebackQueryFactory::new($query)->create();
        }

        /** @var Chargeback */
        return $this->send((new GetPaymentChargebackRequest($paymentId, $chargebackId, $query))->test($testmode));
    }

    /**
     * @param  array|GetPaginatedPaymentChargebacksQuery  $query
     *
     * @throws ApiException
     */
    public function pageFor(Payment $payment, $query = []): ChargebackCollection
    {
        return $this->pageForId($payment->id, $query);
    }

    /**
     * @param  array|GetPaginatedPaymentChargebacksQuery  $query
     *
     * @throws ApiException
     */
    public function pageForId(string $paymentId, $query = [], bool $testmode = false): ChargebackCollection
    {
        if (! $query instanceof GetPaginatedPaymentChargebacksQuery) {
            $testmode = Helpers::extractBool($query, 'testmode', $testmode);
            $query = GetPaginatedPaymentChargebacksQueryFactory::new($query)->create();
        }

        /** @var ChargebackCollection */
        return $this->send((new GetPaginatedPaymentChargebacksRequest($paymentId, $query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over chargebacks for the given payment, retrieved from Mollie.
     *
     * @param  string  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorFor(
        Payment $payment,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->iteratorForId($payment->id, $from, $limit, $parameters, $iterateBackwards);
    }

    /**
     * Create an iterator for iterating over chargebacks for the given payment id, retrieved from Mollie.
     *
     * @param  string  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorForId(
        string $paymentId,
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Helpers::extractBool($filters, 'testmode', false);
        $query = GetPaginatedPaymentChargebacksQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedPaymentChargebacksRequest($paymentId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
