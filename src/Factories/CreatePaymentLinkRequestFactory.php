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
            $this->transformFromPayload('expiresAt', fn (string $date) => $this->getExpiresAt($date), DateTimeImmutable::class),
            $this->payload('allowedMethods'),
        );
    }

    /**
     * This is a backwards compatibility fix.
     * On launch the expiresAt field was accepting a Y-m-d date string, while it should have only accepted Y-m-d\TH:i:s.
     *
     * @return \DateTimeImmutable|null
     */
    protected function getExpiresAt(?string $expiresAt): ?\DateTimeImmutable
    {
        if ($dateString = $this->payload('expiresAt')) {
            // Clean up any extra whitespace in the date string
            $dateString = preg_replace('/\s+/', '', $dateString);
            
            // If the date string doesn't contain time information, add it
            if (! str_contains($dateString, 'T')) {
                $dateString .= 'T00:00:00';
            }

            // Parse with the consistent format
            $expiresAt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $dateString);
        }

        return $expiresAt;
    }
}
