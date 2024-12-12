<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\UpdatePaymentRoutePayloadFactory;
use Mollie\Api\Http\Requests\UpdatePaymentRouteRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Route;

class PaymentRouteEndpointCollection extends EndpointCollection
{
    /**
     * Update the release date for a payment route.
     *
     * @param  string  $releaseDate  UTC datetime in ISO-8601 format when the funds will become available
     *
     * @throws ApiException
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
     * @throws ApiException
     */
    public function updateReleaseDateForId(string $paymentId, string $routeId, string $releaseDate, bool $testmode = false): Route
    {
        $payload = UpdatePaymentRoutePayloadFactory::new([
            'releaseDate' => $releaseDate,
        ])->create();

        /** @var Route */
        return $this->send((new UpdatePaymentRouteRequest($paymentId, $routeId, $payload))->test($testmode));
    }
}
