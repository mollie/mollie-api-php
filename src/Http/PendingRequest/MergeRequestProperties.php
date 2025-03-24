<?php

namespace Mollie\Api\Http\PendingRequest;

use Mollie\Api\Http\PendingRequest;

class MergeRequestProperties
{
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $client = $pendingRequest->getConnector();
        $request = $pendingRequest->getRequest();

        $pendingRequest
            ->headers()
            ->merge(
                $client->headers()->all(),
                $request->headers()->all()
            );

        $pendingRequest
            ->query()
            ->merge(
                $client->query()->all(),
                $request->query()->all()
            );

        return $pendingRequest;
    }
}
