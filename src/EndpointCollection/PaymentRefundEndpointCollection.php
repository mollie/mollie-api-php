<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Factories\CreateRefundPaymentPayloadFactory;
use Mollie\Api\Factories\GetPaginatedPaymentRefundQueryFactory;
use Mollie\Api\Factories\GetPaymentRefundQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Payload\CreateRefundPaymentPayload;
use Mollie\Api\Http\Query\GetPaginatedPaymentRefundQuery;
use Mollie\Api\Http\Query\GetPaymentRefundQuery;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentRefundsRequest;
use Mollie\Api\Http\Requests\GetPaymentRefundRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

class PaymentRefundEndpointCollection extends EndpointCollection
{
    /**
     * Creates a refund for a specific payment.
     *
     * @param Payment $payment
     * @param array $data
     * @param array $filters
     *
     * @return Refund
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createFor(Payment $payment, array $data, array $filters = []): Refund
    {
        return $this->createForId($payment->id, $data, $filters);
    }

    /**
     * Creates a refund for a specific payment.
     *
     * @param string $paymentId
     * @param array $payload
     * @param array $filters
     *
     * @return Refund
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createForId(string $paymentId, $payload = [], array $filters = []): Refund
    {
        if (! $payload instanceof CreateRefundPaymentPayload) {
            $payload = CreateRefundPaymentPayloadFactory::new($payload)
                ->create();
        }

        /** @var Refund */
        return $this->send(new CreatePaymentRefundRequest($paymentId, $payload, $filters));
    }

    /**
     * @param Payment $payment
     * @param string $refundId
     * @param array $parameters
     *
     * @return Refund
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getFor(Payment $payment, string $refundId, array $parameters = []): Refund
    {
        return $this->getForId($payment->id, $refundId, $parameters);
    }

    /**
     * @param string $paymentId
     * @param string $refundId
     * @param array|GetPaymentRefundQuery $query
     *
     * @return Refund
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForId(string $paymentId, string $refundId, $query = []): Refund
    {
        if (! $query instanceof GetPaymentRefundQuery) {
            $query = GetPaymentRefundQueryFactory::new($query)
                ->create();
        }

        /** @var Refund */
        return $this->send(new GetPaymentRefundRequest($paymentId, $refundId, $query));
    }

    /**
     * @param string $paymentId
     * @param array|GetPaginatedPaymentRefundQuery $parameters
     *
     * @return RefundCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pageForId(string $paymentId, $parameters = []): RefundCollection
    {
        return $this->pageFor($paymentId, $parameters);
    }

    /**
     * @param  array|GetPaginatedPaymentRefundQuery  $query
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pageFor(string $paymentId, $query = []): RefundCollection
    {
        if (! $query instanceof GetPaginatedPaymentRefundQuery) {
            $query = GetPaginatedPaymentRefundQueryFactory::new($query)
                ->create();
        }

        return $this->send(new GetPaginatedPaymentRefundsRequest(
            $paymentId,
            $query
        ));
    }

    /**
     * Create an iterator for iterating over refunds for the given payment, retrieved from Mollie.
     *
     * @param Payment $payment
     * @param string|null $from The first resource ID you want to include in your list.
     * @param int|null $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
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
     * Create an iterator for iterating over refunds for the given payment id, retrieved from Mollie.
     *
     * @param string $paymentId
     * @param string|null $from The first resource ID you want to include in your list.
     * @param int|null $limit
     * @param array $filters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iteratorForId(
        string $paymentId,
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $query = GetPaginatedPaymentRefundQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedPaymentRefundsRequest($paymentId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }

    /**
     * @param \Mollie\Api\Resources\Payment $payment
     * @param string $refundId
     * @param array $parameters
     * @return null
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancelForPayment(Payment $payment, string $refundId, $parameters = [])
    {
        return $this->cancelForId($payment->id, $refundId, $parameters);
    }

    /**
     * @param string $paymentId
     * @param string $refundId
     * @param array|bool $testmode
     * @return null
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancelForId(string $paymentId, string $refundId, $testmode = [])
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);

        return $this->send(new CancelPaymentRefundRequest($paymentId, $refundId, $testmode));
    }
}
