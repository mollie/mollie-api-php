<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Http\Data\CreateMandatePayload;

class CreateMandatePayloadFactory extends OldFactory
{
    public function create(): CreateMandatePayload
    {
        if (! $this->has(['method', 'consumerName'])) {
            throw new \InvalidArgumentException('Method and consumerName are required for creating a mandate');
        }

        return new CreateMandatePayload(
            $this->get('method'),
            $this->get('consumerName'),
            $this->get('consumerAccount'),
            $this->get('consumerBic'),
            $this->get('consumerEmail'),
            $this->mapIfNotNull('signatureDate', fn(string $date) => DateTimeImmutable::createFromFormat('Y-m-d', $date)),
            $this->get('mandateReference'),
            $this->get('paypalBillingAgreementId'),
        );
    }
}
