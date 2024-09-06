<?php

namespace Mollie\Api\Helpers;

use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;

class MiddlewareHandlers
{
    protected Handlers $onRequest;

    protected Handlers $onResponse;

    public function __construct()
    {
        $this->onRequest = new Handlers;
        $this->onResponse = new Handlers;
    }

    public function onRequest(callable $callback): static
    {
        $this->onRequest->add(static function (PendingRequest $pendingRequest) use ($callback): PendingRequest {
            $result = $callback($pendingRequest);

            if ($result instanceof PendingRequest) {
                return $result;
            }

            return $pendingRequest;
        });

        return $this;
    }

    public function onResponse(callable $callback): static
    {
        $this->onResponse->add(static function (Response $response) use ($callback): Response {
            $result = $callback($response);

            return $result instanceof Response ? $result : $response;
        });

        return $this;
    }

    public function executeOnRequest(PendingRequest $pendingRequest): PendingRequest
    {
        return $this->onRequest->execute($pendingRequest);
    }

    public function executeOnResponse(Response $response): Response
    {
        return $this->onResponse->execute($response);
    }
}
