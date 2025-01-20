<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Http\Data\CreateSubscriptionPayload;
use Mollie\Api\Http\Data\Metadata;

class CreateSubscriptionPayloadFactory extends OldFactory
{
    public function create(): CreateSubscriptionPayload
    {
        return new CreateSubscriptionPayload(
            MoneyFactory::new($this->get('amount'))->create(),
            $this->get('interval'),
            $this->get('description'),
            $this->get('status'),
            $this->get('times'),
            $this->mapIfNotNull('startDate', fn(string $date) => DateTimeImmutable::createFromFormat('Y-m-d', $date)),
            $this->get('method'),
            $this->mapIfNotNull('applicationFee', fn(array $fee) => ApplicationFeeFactory::new($fee)->create()),
            $this->mapIfNotNull('metadata', Metadata::class),
            $this->get('webhookUrl'),
            $this->get('mandateId'),
            $this->get('profileId'),
        );
    }
}
