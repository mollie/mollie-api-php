<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Route;

class PaymentRouteEndpoint extends RestEndpoint
{
    protected string $resourcePath = "payments_routes";

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Route
    {
        return new Route($this->client);
    }

    /**
     * @param Payment $payment
     * @param string $routeId
     * @param string $releaseDate - UTC datetime in ISO-8601 format when the funds for the following payment will become available on
     * the balance of the connected account
     *
     * @return Route
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function updateReleaseDateFor(Payment $payment, $routeId, $releaseDate): Route
    {
        return $this->updateReleaseDateForPaymentId($payment->id, $routeId, $releaseDate);
    }

    /**
     * @param string $paymentId
     * @param string $routeId
     * @param string $releaseDate - UTC datetime in ISO-8601 format when the funds for the following payment will become available on
     * the balance of the connected account
     *
     * @return \Mollie\Api\Resources\Route
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function updateReleaseDateForPaymentId(string $paymentId, string $routeId, string $releaseDate, bool $testmode = false): Route
    {
        $this->parentId = $paymentId;

        return parent::updateResource($routeId, [
            'releaseDate' => $releaseDate,
            'testmode' => $testmode,
        ]);
    }
}
