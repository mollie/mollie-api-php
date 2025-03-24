<?php

namespace Mollie\Api\Http\PendingRequest;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\PendingRequest;

class MergeBody
{
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $request = $pendingRequest->getRequest();

        if (! $request instanceof HasPayload) {
            return $pendingRequest;
        }

        $body = $request->payload();

        $pendingRequest->setPayload($body);

        if (! $pendingRequest->headers()->get('Content-Type')) {
            $pendingRequest->headers()->add('Content-Type', 'application/json');
        }

        return $pendingRequest;
    }
}
