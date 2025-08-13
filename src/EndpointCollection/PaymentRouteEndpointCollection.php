<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\CreateDelayedPaymentRouteRequestFactory;
use Mollie\Api\Factories\UpdatePaymentRouteRequestFactory;
use Mollie\Api\Http\Requests\ListPaymentRoutesRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Route;
use Mollie\Api\Resources\RouteCollection;

class PaymentRouteEndpointCollection extends EndpointCollection
{
    /**
     * Create a delayed route for a payment.
     *
     * @throws RequestException
     */
    public function createFor(Payment $payment, array $amount, array $destination, ?string $releaseDate = null, bool $testmode = false): Route
    {
        return $this->createForId($payment->id, $amount, $destination, $releaseDate, $testmode);
    }

    /**
     * Create a delayed route for a payment using payment ID.
     *
     * @throws RequestException
     */
    public function createForId(string $paymentId, array $amount, array $destination, ?string $releaseDate = null, bool $testmode = false): Route
    {
        $payload = [
            'amount' => $amount,
            'destination' => $destination,
        ];

        $request = CreateDelayedPaymentRouteRequestFactory::new($paymentId)
            ->withPayload($payload)
            ->create();

        /** @var Route */
        return $this->send($request->test($testmode));
    }

    /**
     * List payment routes for a payment.
     *
     * @throws RequestException
     */
    public function listFor(Payment $payment, bool $testmode = false): RouteCollection
    {
        return $this->listForId($payment->id, $testmode);
    }

    /**
     * List payment routes for a payment using payment ID.
     *
     * @throws RequestException
     */
    public function listForId(string $paymentId, bool $testmode = false): RouteCollection
    {
        $request = new ListPaymentRoutesRequest($paymentId);

        /** @var RouteCollection */
        return $this->send($request->test($testmode));
    }

    /**
     * Update the release date for a payment route.
     *
     * @param  string  $releaseDate  UTC datetime in ISO-8601 format when the funds will become available
     *
     * @throws RequestException
     */
    public function updateReleaseDateFor(Payment $payment, string $routeId, string $releaseDate, bool $testmode = false): Route
    {
        return $this->updateReleaseDateForId($payment->id, $routeId, $releaseDate, $testmode);
    }

    /**
     * Update the release date for a payment route using payment ID.
     *
     * @param  string  $releaseDate  UTC datetime when the funds will become available
     *
     * @throws RequestException
     */
    public function updateReleaseDateForId(string $paymentId, string $routeId, string $releaseDate, bool $testmode = false): Route
    {
        $request = UpdatePaymentRouteRequestFactory::new($paymentId, $routeId)
            ->withPayload([
                'releaseDate' => $releaseDate,
            ])
            ->create();

        /** @var Route */
        return $this->send($request->test($testmode));
    }
}
