<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Http\Data\Metadata;
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
            $this->transformFromPayload('amount', fn (array $amount) => MoneyFactory::new($amount)->create()),
            $this->payload('description'),
            $this->payload('interval'),
            $this->transformFromPayload('startDate', fn (string $date) => DateTimeImmutable::createFromFormat('Y-m-d', $date)),
            $this->payload('times'),
            $this->transformFromPayload('metadata', Metadata::class),
            $this->payload('webhookUrl'),
            $this->payload('mandateId')
        );
    }
}
