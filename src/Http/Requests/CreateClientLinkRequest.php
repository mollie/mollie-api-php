<?php

declare(strict_types=1);

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
    protected ?string $hydratableResource = ClientLink::class;

    public function __construct(
        private Owner $owner,
        private string $name,
        private OwnerAddress $address,
        private ?string $registrationNumber = null,
        private ?string $vatNumber = null,
    ) {
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
