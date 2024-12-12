<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Factories\CreateRefundPaymentPayloadFactory;
use Mollie\Api\Factories\GetPaginatedPaymentRefundQueryFactory;
use Mollie\Api\Factories\GetPaymentRefundQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Data\CreateRefundPaymentPayload;
use Mollie\Api\Http\Data\GetPaymentRefundQuery;
use Mollie\Api\Http\Requests\CancelPaymentRefundRequest;
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
     * @param  array|bool  $testmode
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createFor(Payment $payment, array $data, $testmode = []): Refund
    {
        return $this->createForId($payment->id, $data, $testmode);
    }

    /**
     * Creates a refund for a specific payment.
     *
     * @param  array  $payload
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createForId(string $paymentId, $payload = [], $testmode = []): Refund
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);
        if (! $payload instanceof CreateRefundPaymentPayload) {
            $testmode = Helpers::extractBool($payload, 'testmode', $testmode);
            $payload = CreateRefundPaymentPayloadFactory::new($payload)
                ->create();
        }

        /** @var Refund */
        return $this->send((new CreatePaymentRefundRequest($paymentId, $payload))->test($testmode));
    }

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getFor(Payment $payment, string $refundId, array $parameters = [], bool $testmode = false): Refund
    {
        return $this->getForId($payment->id, $refundId, $parameters, $testmode);
    }

    /**
     * @param  array|GetPaymentRefundQuery  $query
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForId(string $paymentId, string $refundId, $query = [], bool $testmode = false): Refund
    {
        if (! $query instanceof GetPaymentRefundQuery) {
            $testmode = Helpers::extractBool($query, 'testmode', $testmode);
            $query = GetPaymentRefundQueryFactory::new($query)
                ->create();
        }

        /** @var Refund */
        return $this->send((new GetPaymentRefundRequest($paymentId, $refundId, $query))->test($testmode));
    }

    /**
     * @param  array|bool  $testmode
     * @return null
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancelForPayment(Payment $payment, string $refundId, $testmode = [])
    {
        return $this->cancelForId($payment->id, $refundId, $testmode);
    }

    /**
     * @param  array|bool  $testmode
     * @return null
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancelForId(string $paymentId, string $refundId, $testmode = [])
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);

        return $this->send((new CancelPaymentRefundRequest($paymentId, $refundId))->test($testmode));
    }

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pageForId(string $paymentId, ?string $from = null, ?int $limit = null, array $filters = []): RefundCollection
    {
        $testmode = Helpers::extractBool($filters, 'testmode', false);
        $query = GetPaginatedPaymentRefundQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send((new GetPaginatedPaymentRefundsRequest($paymentId, $query))->test($testmode));
    }

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pageFor(Payment $payment, ?string $from = null, ?int $limit = null, array $filters = []): RefundCollection
    {
        return $this->pageForId($payment->id, $from, $limit, $filters);
    }

    /**
     * Create an iterator for iterating over refunds for the given payment, retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
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
     * Create an iterator for iterating over refunds for the given payment id, retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
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
        $query = GetPaginatedPaymentRefundQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedPaymentRefundsRequest($paymentId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
