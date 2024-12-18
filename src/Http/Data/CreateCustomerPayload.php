<?php

namespace Mollie\Api\Http\Data;

class CreateCustomerPayload extends Data
{
    public ?string $name;

    public ?string $email;

    public ?string $locale;

    public ?Metadata $metadata;

    public function __construct(
        ?string $name = null,
        ?string $email = null,
        ?string $locale = null,
        ?Metadata $metadata = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->locale = $locale;
        $this->metadata = $metadata;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'locale' => $this->locale,
            'metadata' => $this->metadata,
        ];
    }
}
