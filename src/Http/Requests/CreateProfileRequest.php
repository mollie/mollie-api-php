<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/profiles-api/create-profile
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Profile>
 */
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
    protected ?string $hydratableResource = Profile::class;

    public function __construct(
        private string $name,
        private string $website,
        private string $email,
        private string $phone,
        private ?string $description = null,
        private ?array $countriesOfActivity = null,
        private ?string $businessCategory = null,
    ) {
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
