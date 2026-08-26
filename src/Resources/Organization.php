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

    /**
     * Required and non-null per the OpenAPI contract (the shared `locale-response`
     * schema lists `null`, but the organization property intersects it with `type: string`).
     */
    public string $locale;

    /**
     * Optional in the API response.
     */
    public ?Address $address = null;

    public ?string $registrationNumber = null;

    public ?string $vatNumber = null;

    public ?string $vatRegulation = null;

    /**
     * @var \stdClass
     */
    public $_links;
}
