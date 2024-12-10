<?php

namespace Mollie\Api\Http\Payload;

class UpdateProfilePayload extends DataBag
{
    public ?string $name;

    public ?string $website;

    public ?string $email;

    public ?string $phone;

    public ?string $description;

    public ?array $countriesOfActivity;

    public ?string $businessCategory;

    public ?string $mode;

    public function __construct(
        ?string $name = null,
        ?string $website = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $description = null,
        ?array $countriesOfActivity = null,
        ?string $businessCategory = null,
        ?string $mode = null
    ) {
        $this->name = $name;
        $this->website = $website;
        $this->email = $email;
        $this->phone = $phone;
        $this->description = $description;
        $this->countriesOfActivity = $countriesOfActivity;
        $this->businessCategory = $businessCategory;
        $this->mode = $mode;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'website' => $this->website,
            'email' => $this->email,
            'phone' => $this->phone,
            'description' => $this->description,
            'countriesOfActivity' => $this->countriesOfActivity,
            'businessCategory' => $this->businessCategory,
            'mode' => $this->mode,
        ];
    }
}
