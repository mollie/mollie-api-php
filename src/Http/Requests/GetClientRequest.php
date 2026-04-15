<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Client;
use Mollie\Api\Types\ClientQuery;
use Mollie\Api\Types\Method;
use Mollie\Api\Utils\Arr;

/**
 * @see https://docs.mollie.com/reference/v2/clients-api/get-client
 */
class GetClientRequest extends ResourceHydratableRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Client::class;

    public function __construct(
        private string $id,
        private ?bool $embedOrganization = null,
        private ?bool $embedOnboarding = null,
    ) {
    }

    protected function defaultQuery(): array
    {
        return [
            'embed' => Arr::join([
                $this->embedOrganization ? ClientQuery::EMBED_ORGANIZATION : null,
                $this->embedOnboarding ? ClientQuery::EMBED_ONBOARDING : null,
            ]),
        ];
    }

    public function resolveResourcePath(): string
    {
        return "clients/{$this->id}";
    }
}
