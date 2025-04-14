<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Recipient;

class RecipientFactory extends Factory
{
    public function create(): Recipient
    {
        return new Recipient(
            $this->get('type'),
            $this->get('email'),
            $this->get('streetAndNumber'),
            $this->get('postalCode'),
            $this->get('city'),
            $this->get('country'),
            $this->get('locale'),
            $this->get('title'),
            $this->get('givenName'),
            $this->get('familyName'),
            $this->get('organizationName'),
            $this->get('organizationNumber'),
            $this->get('vatNumber'),
            $this->get('phone'),
            $this->get('streetAdditional'),
            $this->get('region'),
        );
    }
}
