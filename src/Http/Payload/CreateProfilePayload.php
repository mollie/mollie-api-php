<?php

namespace Mollie\Api\Http\Payload;

class CreateProfilePayload extends DataBag
{
    public string $name;

    public string $website;

    public string $email;

    public string $phone;

    public ?string $description;

    public ?array $countriesOfActivity;

    public ?string $businessCategory;

    public function __construct(
        string $name,
        string $website,
        string $email,
        string $phone,
        ?string $description = null,
        ?array $countriesOfActivity = null,
        ?string $businessCategory = null
    ) {
        $this->name = $name;
        $this->website = $website;
        $this->email = $email;
        $this->phone = $phone;
        $this->description = $description;
        $this->countriesOfActivity = $countriesOfActivity;
        $this->businessCategory = $businessCategory;
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
        ];
    }
}
