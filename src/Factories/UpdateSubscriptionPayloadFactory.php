<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Http\Payload\Metadata;
use Mollie\Api\Http\Payload\UpdateSubscriptionPayload;

class UpdateSubscriptionPayloadFactory extends Factory
{
    public function create(): UpdateSubscriptionPayload
    {
        return new UpdateSubscriptionPayload(
            $this->mapIfNotNull('amount', fn (array $amount) => MoneyFactory::new($amount)->create()),
            $this->get('description'),
            $this->get('interval'),
            $this->mapIfNotNull('startDate', fn (string $date) => DateTimeImmutable::createFromFormat('Y-m-d', $date)),
            $this->get('times'),
            $this->mapIfNotNull('metadata', Metadata::class),
            $this->get('webhookUrl'),
            $this->get('mandateId')
        );
    }
}
