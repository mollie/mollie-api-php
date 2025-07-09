<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use DateTimeInterface;
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
            $this->getExpiresAt(),
            $this->payload('allowedMethods'),
        );
    }

    /**
     * This is a backwards compatibility fix.
     * On launch the expiresAt field was accepting a Y-m-d date string, while it should have only accepted Y-m-d\TH:i:sP.
     *
     * @return \DateTimeImmutable|null
     */
    protected function getExpiresAt(): ?\DateTimeImmutable
    {
        $expiresAt = null;

        if ($dateString = $this->payload('expiresAt')) {

            // If the date string doesn't contain time information, add it
            if (! str_contains($dateString, 'T')) {
                $dateString .= 'T00:00:00';
            }

            // If the date string doesn't contain timezone information, add UTC
            if (!preg_match('/[+-][0-9]{2}:?[0-9]{2}$/', $dateString)) {
                $dateString .= '+00:00';
            }

            // Parse with the consistent format
            $expiresAt = DateTimeImmutable::createFromFormat(DateTimeInterface::ISO8601, $dateString);
        }

        return $expiresAt;
    }
}
