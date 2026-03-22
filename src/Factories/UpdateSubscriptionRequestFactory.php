<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Date;
use Mollie\Api\Http\Requests\UpdateSubscriptionRequest;

class UpdateSubscriptionRequestFactory extends RequestFactory
{
    private string $customerId;

    private string $subscriptionId;

    public function __construct(string $customerId, string $subscriptionId)
    {
        $this->customerId = $customerId;
        $this->subscriptionId = $subscriptionId;
    }

    public function create(): UpdateSubscriptionRequest
    {
        return new UpdateSubscriptionRequest(
            $this->customerId,
            $this->subscriptionId,
            $this->transformFromPayload('amount', fn ($amount) => MoneyFactory::new($amount)->create()),
            $this->payload('description'),
            $this->payload('interval'),
            $this->transformFromPayload('startDate', fn (string $date) => new Date($date), Date::class),
            $this->payload('times'),
            $this->payload('metadata'),
            $this->payload('webhookUrl'),
            $this->payload('mandateId')
        );
    }
}
