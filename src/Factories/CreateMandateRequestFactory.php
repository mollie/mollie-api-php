<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Http\Requests\CreateMandateRequest;

class CreateMandateRequestFactory extends RequestFactory
{
    private string $customerId;

    public function __construct(string $customerId)
    {
        $this->customerId = $customerId;
    }

    public function create(): CreateMandateRequest
    {
        if (! $this->payloadHas(['method', 'consumerName'])) {
            throw new LogicException('Method and consumerName are required for creating a mandate');
        }

        return new CreateMandateRequest(
            $this->customerId,
            $this->payload('method'),
            $this->payload('consumerName'),
            $this->payload('consumerAccount'),
            $this->payload('consumerBic'),
            $this->payload('consumerEmail'),
            $this->transformFromPayload('signatureDate', fn (string $date) => DateTimeImmutable::createFromFormat('Y-m-d', $date), DateTimeImmutable::class),
            $this->payload('mandateReference'),
            $this->payload('paypalBillingAgreementId'),
        );
    }
}
