<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Http\Requests\UpdatePaymentRouteRequest;

class UpdatePaymentRouteRequestFactory extends RequestFactory
{
    private string $paymentId;

    private string $routeId;

    public function __construct(string $paymentId, string $routeId)
    {
        $this->paymentId = $paymentId;
        $this->routeId = $routeId;
    }

    public function create(): UpdatePaymentRouteRequest
    {
        if (! $releaseDate = $this->payload('releaseDate')) {
            throw new LogicException('Release date is required');
        }

        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d', $releaseDate);

        if ($dateTime === false) {
            throw new LogicException('Invalid release date format. Expected Y-m-d');
        }

        return new UpdatePaymentRouteRequest(
            $this->paymentId,
            $this->routeId,
            $dateTime,
        );
    }
}
