<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Data\Owner;
use Mollie\Api\Http\Data\OwnerAddress;
use Mollie\Api\Resources\ClientLink;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreateClientLinkRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = ClientLink::class;

    private Owner $owner;

    private string $name;

    private OwnerAddress $address;

    private ?string $registrationNumber;

    private ?string $vatNumber;

    public function __construct(
        Owner $owner,
        string $name,
        OwnerAddress $address,
        ?string $registrationNumber = null,
        ?string $vatNumber = null
    ) {
        $this->owner = $owner;
        $this->name = $name;
        $this->address = $address;
        $this->registrationNumber = $registrationNumber;
        $this->vatNumber = $vatNumber;
    }

    protected function defaultPayload(): array
    {
        return [
            'owner' => $this->owner,
            'name' => $this->name,
            'address' => $this->address,
            'registrationNumber' => $this->registrationNumber,
            'vatNumber' => $this->vatNumber,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'client-links';
    }
}
