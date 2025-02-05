<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreateProfileRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Profile::class;

    private string $name;

    private string $website;

    private string $email;

    private string $phone;

    private ?string $description;

    private ?array $countriesOfActivity;

    private ?string $businessCategory;

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
        ];
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'profiles';
    }
}
