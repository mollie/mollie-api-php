<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;

class CreatePaymentLinkRequestFactory extends RequestFactory
{
    public function create(): CreatePaymentLinkRequest
    {
        return new CreatePaymentLinkRequest(
            $this->payload('description'),
            $this->transformFromPayload('amount', fn ($amount) => MoneyFactory::new($amount)->create()),
            $this->payload('redirectUrl'),
            $this->payload('webhookUrl'),
            $this->payload('profileId'),
            $this->payload('reusable'),
            $this->transformFromPayload('expiresAt', fn (string $date) => DateTimeImmutable::createFromFormat('Y-m-d', $date), DateTimeImmutable::class),
            $this->payload('allowedMethods'),
        );
    }
}
