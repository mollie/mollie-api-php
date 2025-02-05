<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Factories\CreatePaymentRefundRequestFactory;
use Mollie\Api\Factories\GetPaginatedPaymentRefundsRequestFactory;
use Mollie\Api\Factories\GetPaymentRefundRequestFactory;
use Mollie\Api\Http\Requests\CancelPaymentRefundRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Utils\Utility;

class PaymentRefundEndpointCollection extends EndpointCollection
{
    /**
     * Creates a refund for a specific payment.
     *
     * @param  bool|array  $testmode
     *
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function createFor(Payment $payment, array $data, $testmode = false): Refund
    {
        return $this->createForId($payment->id, $data, $testmode);
    }

    /**
     * Creates a refund for a specific payment.
     *
     * @param  bool|array  $testmode
     *
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function createForId(string $paymentId, array $payload = [], $testmode = false): Refund
    {
        $testmode = Utility::extractBool($payload, 'testmode', false) ?:
            Utility::extractBool($testmode, 'testmode', false);

        $request = CreatePaymentRefundRequestFactory::new($paymentId)
            ->withPayload($payload)
            ->create();

        return $this->send($request->test($testmode));
    }

    /**
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function getFor(Payment $payment, string $refundId, array $parameters = [], bool $testmode = false): Refund
    {
        return $this->getForId($payment->id, $refundId, $parameters, $testmode);
    }

    /**
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function getForId(string $paymentId, string $refundId, array $query = [], bool $testmode = false): Refund
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $request = GetPaymentRefundRequestFactory::new($paymentId, $refundId)
            ->withQuery($query)
            ->create();

        /** @var Refund */
        return $this->send($request->test($testmode));
    }

    /**
     * @param  array|bool  $testmode
     * @return null
     *
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function cancelForPayment(Payment $payment, string $refundId, $testmode = false)
    {
        return $this->cancelForId($payment->id, $refundId, $testmode);
    }

    /**
     * @param  array|bool  $testmode
     * @return null
     *
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function cancelForId(string $paymentId, string $refundId, $testmode = false)
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        return $this->send((new CancelPaymentRefundRequest($paymentId, $refundId))->test($testmode));
    }

    /**
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function pageForId(string $paymentId, ?string $from = null, ?int $limit = null, array $filters = []): RefundCollection
    {
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $request = GetPaginatedPaymentRefundsRequestFactory::new($paymentId)
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        return $this->send($request->test($testmode));
    }

    /**
     * @throws \Mollie\Api\Exceptions\RequestException
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
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $request = GetPaginatedPaymentRefundsRequestFactory::new($paymentId)
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
