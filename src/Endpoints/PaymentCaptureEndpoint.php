<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;

class PaymentCaptureEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "payments_captures";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Capture::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = CaptureCollection::class;

    /**
     * Creates a payment capture in Mollie.
     *
     * @param Payment $payment.
     * @param array $data An array containing details on the capture.
     * @param array $filters
     *
     * @return Capture
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createFor(Payment $payment, array $data = [], array $filters = []): Capture
    {
        return $this->createForId($payment->id, $data, $filters);
    }

    /**
     * Creates a payment capture in Mollie.
     *
     * @param string $paymentId The payment's ID.
     * @param array $data An array containing details on the capture.
     * @param array $filters
     *
     * @return Capture
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createForId(string $paymentId, array $data = [], array $filters = []): Capture
    {
        $this->parentId = $paymentId;

        /** @var Capture */
        return $this->createResource($data, $filters);
    }

    /**
     * @param Payment $payment
     * @param string $captureId
     * @param array $parameters
     *
     * @return Capture
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getFor(Payment $payment, string $captureId, array $parameters = []): Capture
    {
        return $this->getForId($payment->id, $captureId, $parameters);
    }

    /**
     * @param string $paymentId
     * @param string $captureId
     * @param array $parameters
     *
     * @return \Mollie\Api\Resources\Capture
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForId(string $paymentId, string $captureId, array $parameters = []): Capture
    {
        $this->parentId = $paymentId;

        /** @var Capture */
        return $this->readResource($captureId, $parameters);
    }

    /**
     * @param Payment $payment
     * @param array $parameters
     *
     * @return CaptureCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listFor(Payment $payment, array $parameters = []): CaptureCollection
    {
        return $this->listForId($payment->id, $parameters);
    }

    /**
     * Create an iterator for iterating over captures for the given payment, retrieved from Mollie.
     *
     * @param Payment $payment
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
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
     * @param string $paymentId
     * @param array $parameters
     *
     * @return CaptureCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForId(string $paymentId, array $parameters = []): CaptureCollection
    {
        $this->parentId = $paymentId;

        /** @var CaptureCollection */
        return $this->fetchCollection(null, null, $parameters);
    }

    /**
     * Create an iterator for iterating over captures for the given payment id, retrieved from Mollie.
     *
     * @param string $paymentId
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iteratorForId(
        string $paymentId,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $this->parentId = $paymentId;

        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
