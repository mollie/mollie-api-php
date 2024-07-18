<?php

namespace Mollie\Api\Http\EndpointCollection;

use Mollie\Api\Http\BaseEndpointCollection;
use Mollie\Api\Http\Requests\GetPaginatedPaymentsRequest;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Http\Requests\RefundPaymentRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Refund;

class PaymentEndpointCollection extends BaseEndpointCollection
{
    /**
     * Get the balance endpoint.
     *
     * @return PaymentCollection
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): PaymentCollection
    {
        return $this->send(new GetPaginatedPaymentsRequest($from, $limit, $filters));
    }

    /**
     * Retrieve a single payment from Mollie.
     *
     * Will throw a ApiException if the payment id is invalid or the resource cannot be found.
     *
     * @param string $paymentId
     * @param array $filters
     *
     * @return Payment
     * @throws ApiException
     */
    public function get(string $paymentId, array $filters = []): Payment
    {
        return $this->send(new GetPaymentRequest($paymentId, $filters));
    }

    /**
     * Issue a refund for the given payment.
     *
     * The $data parameter may either be an array of endpoint parameters, a float value to
     * initiate a partial refund, or empty to do a full refund.
     *
     * @param Payment $payment
     * @param array|float|null $data
     *
     * @return Refund
     * @throws ApiException
     */
    public function refund(Payment $payment, $data = []): Refund
    {
        return $this->send(new RefundPaymentRequest($payment->id, $data));
    }

    /**
     * Create an iterator for iterating over payments retrieved from Mollie.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $filters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        return $this->send(
            (new GetPaginatedPaymentsRequest($from, $limit, $filters))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
