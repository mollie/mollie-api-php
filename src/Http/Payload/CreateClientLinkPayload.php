<?php

namespace Mollie\Api\Http\Payload;

class CreateClientLinkPayload extends DataBag
{
    public Owner $owner;

    public string $name;

    public OwnerAddress $address;

    public ?string $registrationNumber;

    public ?string $vatNumber;

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

    public function toArray(): array
    {
        return [
            'owner' => $this->owner,
            'name' => $this->name,
            'address' => $this->address,
            'registrationNumber' => $this->registrationNumber,
            'vatNumber' => $this->vatNumber,
        ];
    }
}
