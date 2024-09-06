<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Helpers\Arr;
use Mollie\Api\Http\Request;
use Mollie\Api\Resources\Client;
use Mollie\Api\Rules\Included;
use Mollie\Api\Types\ClientQuery;
use Mollie\Api\Types\Method;

class GetClientRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Client::class;

    private string $id;

    private array $embed;

    public function __construct(string $id, array $embed = [])
    {
        $this->id = $id;
        $this->embed = $embed;
    }

    protected function defaultQuery(): array
    {
        return [
            'embed' => Arr::join($this->embed),
        ];
    }

    public function rules(): array
    {
        return [
            'embed' => Included::in(ClientQuery::class),
        ];
    }

    public function resolveResourcePath(): string
    {
        return "clients/{$this->id}";
    }
}
