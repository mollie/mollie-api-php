<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\GetPaginatedPaymentChargebacksRequestFactory;
use Mollie\Api\Factories\GetPaymentChargebackRequestFactory;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Utils\Utility;

class PaymentChargebackEndpointCollection extends EndpointCollection
{
    /**
     * @throws RequestException
     */
    public function getFor(Payment $payment, string $chargebackId, array $query = [], bool $testmode = false): Chargeback
    {
        return $this->getForId($payment->id, $chargebackId, $query, $testmode);
    }

    /**
     * @throws RequestException
     */
    public function getForId(string $paymentId, string $chargebackId, array $query = [], bool $testmode = false): Chargeback
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $request = GetPaymentChargebackRequestFactory::new($paymentId, $chargebackId)
            ->withQuery($query)
            ->create();

        /** @var Chargeback */
        return $this->send($request->test($testmode));
    }

    /**
     * @throws RequestException
     */
    public function pageFor(Payment $payment, array $query = []): ChargebackCollection
    {
        return $this->pageForId($payment->id, $query);
    }

    /**
     * @throws RequestException
     */
    public function pageForId(string $paymentId, array $query = [], bool $testmode = false): ChargebackCollection
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $request = GetPaginatedPaymentChargebacksRequestFactory::new($paymentId)
            ->withQuery($query)
            ->create();

        /** @var ChargebackCollection */
        return $this->send($request->test($testmode));
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
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $request = GetPaginatedPaymentChargebacksRequestFactory::new($paymentId)
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
                ->test($testmode)
        );
    }
}
