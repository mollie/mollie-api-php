<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class UpdateProfileRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Profile::class;

    private string $id;

    private ?string $name;

    private ?string $website;

    private ?string $email;

    private ?string $phone;

    private ?string $description;

    private ?array $countriesOfActivity;

    private ?string $businessCategory;

    private ?string $mode;

    public function __construct(
        string $id,
        ?string $name = null,
        ?string $website = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $description = null,
        ?array $countriesOfActivity = null,
        ?string $businessCategory = null,
        ?string $mode = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->website = $website;
        $this->email = $email;
        $this->phone = $phone;
        $this->description = $description;
        $this->countriesOfActivity = $countriesOfActivity;
        $this->businessCategory = $businessCategory;
        $this->mode = $mode;
    }

    protected function defaultPayload(): array
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

    public function resolveResourcePath(): string
    {
        return "profiles/{$this->id}";
    }
}
