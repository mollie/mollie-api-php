<?php

namespace Mollie\Api\Http\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Endpoint;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Requests\CreateRefundPaymentRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentsRequest;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Http\Requests\UpdatePaymentRequest;
use Mollie\Api\Http\Rules\InvalidIdRule;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Refund;

class PaymentEndpointCollection extends Endpoint
{
    /**
     * Creates a payment in Mollie.
     *
     * @param  array  $data  An array containing details on the payment.
     *
     * @throws ApiException
     */
    public function create(array $data = [], array $filters = []): Payment
    {
        /** @var Payment */
        return $this->send(new CreatePaymentRequest($data, $filters));
    }

    /**
     * Update the given Payment.
     *
     * Will throw a ApiException if the payment id is invalid or the resource cannot be found.
     *
     * @param  string  $paymentId
     *
     * @throws ApiException
     */
    public function update($paymentId, array $data = []): ?Payment
    {
        /** @var null|Payment */
        return $this
            ->validateWith(new InvalidIdRule(id: $paymentId, prefix: Payment::$resourceIdPrefix))
            ->send(new UpdatePaymentRequest($paymentId, $data));
    }

    /**
     * Get the balance endpoint.
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): PaymentCollection
    {
        return $this->send(new GetPaginatedPaymentsRequest(
            filters: $filters,
            from: $from,
            limit: $limit
        ));
    }

    /**
     * Retrieve a single payment from Mollie.
     *
     * Will throw a ApiException if the payment id is invalid or the resource cannot be found.
     *
     *
     * @throws ApiException
     */
    public function get(string $paymentId, array $filters = []): Payment
    {
        return $this
            ->validateWith(new InvalidIdRule(id: $paymentId, prefix: Payment::$resourceIdPrefix))
            ->send(new GetPaymentRequest($paymentId, $filters));
    }

    /**
     * Issue a refund for the given payment.
     *
     * The $data parameter may either be an array of endpoint parameters, a float value to
     * initiate a partial refund, or empty to do a full refund.
     *
     * @param  array|float|null  $data
     *
     * @throws ApiException
     */
    public function refund(Payment $payment, $data = []): Refund
    {
        return $this->send(new CreateRefundPaymentRequest($payment->id, $data));
    }

    /**
     * Create an iterator for iterating over payments retrieved from Mollie.
     *
     * @param  string  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        return $this->send(
            (new GetPaginatedPaymentsRequest(filters: $filters, from: $from, limit: $limit))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }

    /**
     * @todo: Implement the rules method. Standardize request ids!
     */
    protected function rules(Request $request): array
    {
        return [
            new InvalidIdRule(id: $request->getId(), prefix: Payment::$resourceIdPrefix),
        ];
    }
}
