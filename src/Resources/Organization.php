<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Address;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Organization extends BaseResource
{
    public string $id;

    public string $name;

    public string $email;

    public string $locale;

    public Address $address;

    public string $registrationNumber;

    public string $vatNumber;

    public ?string $vatRegulation = null;

    /**
     * @var \stdClass
     */
    public $_links;
}
