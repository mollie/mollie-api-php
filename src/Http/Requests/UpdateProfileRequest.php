<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/profiles-api/update-profile
 */
class UpdateProfileRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Profile::class;

    public function __construct(
        private string $id,
        private ?string $name = null,
        private ?string $website = null,
        private ?string $email = null,
        private ?string $phone = null,
        private ?string $description = null,
        private ?array $countriesOfActivity = null,
        private ?string $businessCategory = null,
        private ?string $mode = null,
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
            'mode' => $this->mode,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "profiles/{$this->id}";
    }
}
