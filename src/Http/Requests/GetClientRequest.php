<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Client;
use Mollie\Api\Types\ClientQuery;
use Mollie\Api\Types\Method;
use Mollie\Api\Utils\Arr;

class GetClientRequest extends ResourceHydratableRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Client::class;

    private string $id;

    private ?bool $embedOrganization;

    private ?bool $embedOnboarding;

    public function __construct(string $id, ?bool $embedOrganization = null, ?bool $embedOnboarding = null)
    {
        $this->id = $id;
        $this->embedOrganization = $embedOrganization;
        $this->embedOnboarding = $embedOnboarding;
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
