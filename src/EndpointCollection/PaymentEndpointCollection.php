<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\CreatePaymentRefundRequestFactory;
use Mollie\Api\Factories\CreatePaymentRequestFactory;
use Mollie\Api\Factories\GetPaymentRequestFactory;
use Mollie\Api\Factories\SortablePaginatedQueryFactory;
use Mollie\Api\Factories\UpdatePaymentRequestFactory;
use Mollie\Api\Http\Requests\CancelPaymentRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentsRequest;
use Mollie\Api\Http\Requests\ReleasePaymentAuthorizationRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Utils\Utility;

class PaymentEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a single payment from Mollie.
     *
     * Will throw a ApiException if the payment id is invalid or the resource cannot be found.
     *
     * @throws RequestException
     */
    public function get(string $id, array $query = [], bool $testmode = false): Payment
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $request = GetPaymentRequestFactory::new($id)
            ->withQuery($query)
            ->create();

        return $this->send($request->test($testmode));
    }

    /**
     * Creates a payment in Mollie.
     *
     * @param  array  $payload  An array containing details on the payment.
     * @param  array  $query  An array of strings or a single string containing the details to include.
     *
     * @throws RequestException
     */
    public function create(array $payload = [], array $query = [], bool $testmode = false): Payment
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $request = CreatePaymentRequestFactory::new()
            ->withPayload($payload)
            ->withQuery($query)
            ->create();

        /** @var Payment */
        return $this->send($request->test($testmode));
    }

    /**
     * Update the given Payment.
     *
     * Will throw a ApiException if the payment id is invalid or the resource cannot be found.
     *
     * @throws RequestException
     */
    public function update(string $id, array $data = [], bool $testmode = false): ?Payment
    {
        $testmode = Utility::extractBool($data, 'testmode', $testmode);

        $request = UpdatePaymentRequestFactory::new($id)
            ->withPayload($data)
            ->create();

        /** @var null|Payment */
        return $this->send($request->test($testmode));
    }

    /**
     * Deletes the given Payment.
     *
     * Will throw a ApiException if the payment id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @throws RequestException
     */
    public function delete(string $id, $data = []): Payment
    {
        return $this->cancel($id, $data);
    }

    /**
     * Cancel the given Payment. This is just an alias of the 'delete' method.
     *
     * Will throw a ApiException if the payment id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @param  array|bool  $testmode
     *
     * @throws RequestException
     */
    public function cancel(string $id, $testmode = false): Payment
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        /** @var Payment */
        return $this->send((new CancelPaymentRequest($id))->test($testmode));
    }

    /**
     * Issue a refund for the given payment.
     *
     * The $data parameter may either be an array of endpoint
     * parameters, or an instance of CreateRefundPaymentData.
     *
     *
     * @throws RequestException
     */
    public function refund(Payment $payment, array $payload = [], bool $testmode = false): Refund
    {
        $testmode = Utility::extractBool($payload, 'testmode', $testmode);

        $request = CreatePaymentRefundRequestFactory::new($payment->id)
            ->withPayload($payload)
            ->create();

        return $this->send($request->test($testmode));
    }

    /**
     * Release the authorization for the given payment.
     *
     * @param  Payment|string  $paymentId
     * @return AnyResource|Response
     *
     * @throws RequestException
     */
    public function releaseAuthorization($paymentId)
    {
        $paymentId = $paymentId instanceof Payment ? $paymentId->id : $paymentId;

        return $this->send(new ReleasePaymentAuthorizationRequest($paymentId));
    }

    /**
     * Get the balance endpoint.
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): PaymentCollection
    {
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $query = SortablePaginatedQueryFactory::new()
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        return $this->send((new GetPaginatedPaymentsRequest(
            $query->from,
            $query->limit,
            $query->sort,
        ))->test($testmode));
    }

    /**
     * Create an iterator for iterating over payments retrieved from Mollie.
     *
     * @param  string  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $query = SortablePaginatedQueryFactory::new()
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        return $this->send(
            (new GetPaginatedPaymentsRequest(
                $query->from,
                $query->limit,
                $query->sort,
            ))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
