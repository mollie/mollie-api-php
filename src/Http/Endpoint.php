<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\HasBody;
use Mollie\Api\Http\Requests\Request;
use Mollie\Api\MollieApiClient;

abstract class Endpoint
{
    use HandlesQueries;
    use HandlesResourceBuilding;
    use HandlesValidation;

    protected MollieApiClient $client;

    public function __construct(MollieApiClient $client)
    {
        $this->client = $client;
    }

    public function send(Request $request): mixed
    {
        $this->validate($request);

        $path = $request->resolveResourcePath()
            .$this->buildQueryString($request->getQuery());

        $body = $request instanceof HasBody
            ? $request->getBody()
            : null;

        $result = $this->client->performHttpCall(
            $request->getMethod(),
            $path,
            $body
        );

        if ($result->isEmpty()) {
            return null;
        }

        return $this->build($request, $result);
    }
}
