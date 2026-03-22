<?php

namespace Mollie\Api\Factories;

use DateTimeInterface;
use Mollie\Api\Http\Data\DateTime;
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
            $this->transformFromPayload('expiresAt', fn ($date) => $this->getExpiresAt($date), DateTime::class),
            $this->payload('allowedMethods'),
            $this->payload('sequenceType'),
            $this->payload('customerId'),
        );
    }

    /**
     * This is a backwards compatibility fix.
     * On launch the expiresAt field was accepting a Y-m-d date string, while it should have only accepted Y-m-d\TH:i:sP.
     * We now need to support both formats.
     *
     * @param  null|string|DateTimeInterface  $date
     */
    protected function getExpiresAt($date): ?DateTime
    {
        if (is_null($date)) {
            return null;
        }

        if ($date instanceof DateTimeInterface) {
            return new DateTime($date);
        }

        // If the date string doesn't contain time information, add it
        /** @var string $date */
        if (strpos($date, 'T') === false) {
            $date .= 'T00:00:00';
        }

        // If the date string doesn't contain timezone information, add UTC
        if (! preg_match('/[+-][0-9]{2}:?[0-9]{2}$/', $date)) {
            $date .= '+00:00';
        }

        return new DateTime($date);
    }
}
