<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\CreatePaymentCapturePayloadFactory;
use Mollie\Api\Factories\GetPaginatedPaymentCapturesQueryFactory;
use Mollie\Api\Factories\GetPaymentCaptureQueryFactory;
use Mollie\Api\Utils\Utility;
use Mollie\Api\Http\Data\CreatePaymentCapturePayload;
use Mollie\Api\Http\Data\GetPaginatedPaymentCapturesQuery;
use Mollie\Api\Http\Data\GetPaymentCaptureQuery;
use Mollie\Api\Http\Requests\CreatePaymentCaptureRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentCapturesRequest;
use Mollie\Api\Http\Requests\GetPaymentCaptureRequest;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;

class PaymentCaptureEndpointCollection extends EndpointCollection
{
    /**
     * Creates a payment capture in Mollie.
     *
     * @param  array|CreatePaymentCapturePayload  $payload  An array containing details on the capture.
     *
     * @throws ApiException
     */
    public function createFor(Payment $payment, $payload = [], ?bool $testmode = null): Capture
    {
        return $this->createForId($payment->id, $payload, $testmode);
    }

    /**
     * Creates a payment capture in Mollie.
     *
     * @param  array|CreatePaymentCapturePayload  $payload  An array containing details on the capture.
     *
     * @throws ApiException
     */
    public function createForId(string $paymentId, $payload = [], bool $testmode = false): Capture
    {
        if (! $payload instanceof CreatePaymentCapturePayload) {
            $testmode = Utility::extractBool($payload, 'testmode', $testmode);
            $payload = CreatePaymentCapturePayloadFactory::new($payload)->create();
        }

        /** @var Capture */
        return $this->send((new CreatePaymentCaptureRequest($paymentId, $payload))->test($testmode));
    }

    /**
     * @param  array|GetPaymentCaptureQuery  $query
     *
     * @throws ApiException
     */
    public function getFor(Payment $payment, string $captureId, $query = [], ?bool $testmode = null): Capture
    {
        return $this->getForId($payment->id, $captureId, $query, $testmode);
    }

    /**
     * @param  array|GetPaymentCaptureQuery  $query
     *
     * @throws ApiException
     */
    public function getForId(string $paymentId, string $captureId, $query = [], bool $testmode = false): Capture
    {
        if (! $query instanceof GetPaymentCaptureQuery) {
            $testmode = Utility::extractBool($query, 'testmode', $testmode);
            $query = GetPaymentCaptureQueryFactory::new($query)->create();
        }

        /** @var Capture */
        return $this->send((new GetPaymentCaptureRequest($paymentId, $captureId, $query))->test($testmode));
    }

    /**
     * @param  array|GetPaginatedPaymentCapturesQuery  $query
     *
     * @throws ApiException
     */
    public function pageFor(Payment $payment, $query = [], ?bool $testmode = null): CaptureCollection
    {
        return $this->pageForId($payment->id, $query, $testmode);
    }

    /**
     * @param  array|GetPaginatedPaymentCapturesQuery  $query
     *
     * @throws ApiException
     */
    public function pageForId(string $paymentId, $query = [], bool $testmode = false): CaptureCollection
    {
        if (! $query instanceof GetPaginatedPaymentCapturesQuery) {
            $testmode = Utility::extractBool($query, 'testmode', $testmode);
            $query = GetPaginatedPaymentCapturesQueryFactory::new($query)->create();
        }

        /** @var CaptureCollection */
        return $this->send((new GetPaginatedPaymentCapturesRequest($paymentId, $query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over captures for the given payment, retrieved from Mollie.
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
     * Create an iterator for iterating over captures for the given payment id, retrieved from Mollie.
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
        $query = GetPaginatedPaymentCapturesQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedPaymentCapturesRequest($paymentId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
