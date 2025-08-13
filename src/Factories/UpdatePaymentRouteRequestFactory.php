<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Http\Data\Date;
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
        if (! $this->payloadHas('releaseDate')) {
            throw new LogicException('Release date is required');
        }

        return new UpdatePaymentRouteRequest(
            $this->paymentId,
            $this->routeId,
            $this->transformFromPayload('releaseDate', fn ($date) => new Date($date), Date::class),
        );
    }
}
