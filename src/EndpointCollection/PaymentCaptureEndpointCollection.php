<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\CreatePaymentCaptureRequestFactory;
use Mollie\Api\Factories\GetPaginatedPaymentCapturesRequestFactory;
use Mollie\Api\Factories\GetPaymentCaptureRequestFactory;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Utils\Utility;

class PaymentCaptureEndpointCollection extends EndpointCollection
{
    /**
     * Creates a payment capture in Mollie.
     *
     * @throws RequestException
     */
    public function createFor(Payment $payment, array $payload = [], bool $testmode = false): Capture
    {
        return $this->createForId($payment->id, $payload, $testmode);
    }

    /**
     * Creates a payment capture in Mollie.
     *
     * @throws RequestException
     */
    public function createForId(string $paymentId, array $payload = [], bool $testmode = false): Capture
    {
        $testmode = Utility::extractBool($payload, 'testmode', $testmode);

        $request = CreatePaymentCaptureRequestFactory::new($paymentId)
            ->withPayload($payload)
            ->create();

        /** @var Capture */
        return $this->send($request->test($testmode));
    }

    /**
     * @throws RequestException
     */
    public function getFor(Payment $payment, string $captureId, array $query = [], bool $testmode = false): Capture
    {
        return $this->getForId($payment->id, $captureId, $query, $testmode);
    }

    /**
     * @throws RequestException
     */
    public function getForId(string $paymentId, string $captureId, array $query = [], bool $testmode = false): Capture
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $request = GetPaymentCaptureRequestFactory::new($paymentId, $captureId)
            ->withQuery($query)
            ->create();

        /** @var Capture */
        return $this->send($request->test($testmode));
    }

    /**
     * @throws RequestException
     */
    public function pageFor(Payment $payment, array $query = [], bool $testmode = false): CaptureCollection
    {
        return $this->pageForId($payment->id, $query, $testmode);
    }

    /**
     * @throws RequestException
     */
    public function pageForId(string $paymentId, array $query = [], bool $testmode = false): CaptureCollection
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $request = GetPaginatedPaymentCapturesRequestFactory::new($paymentId)
            ->withQuery($query)
            ->create();

        /** @var CaptureCollection */
        return $this->send($request->test($testmode));
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

        $request = GetPaginatedPaymentCapturesRequestFactory::new($paymentId)
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
