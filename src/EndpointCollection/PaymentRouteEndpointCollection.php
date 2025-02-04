<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\UpdatePaymentRouteRequestFactory;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Route;

class PaymentRouteEndpointCollection extends EndpointCollection
{
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
