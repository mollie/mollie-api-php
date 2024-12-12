<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Types\Method;

class ApplyIdempotencyKey
{
    const IDEMPOTENCY_KEY_HEADER = 'Idempotency-Key';

    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        if (! $this->isMutatingRequest($pendingRequest)) {
            $pendingRequest->headers()->remove(self::IDEMPOTENCY_KEY_HEADER);

            return $pendingRequest;
        }

        $idempotencyKey = $pendingRequest->getConnector()->getIdempotencyKey();
        $idempotencyKeyGenerator = $pendingRequest->getConnector()->getIdempotencyKeyGenerator();

        if ($idempotencyKey === null && $idempotencyKeyGenerator === null) {
            return $pendingRequest;
        }

        $pendingRequest->headers()->add(
            self::IDEMPOTENCY_KEY_HEADER,
            $idempotencyKey ?? $idempotencyKeyGenerator->generate()
        );

        return $pendingRequest;
    }

    private function isMutatingRequest(PendingRequest $pendingRequest): bool
    {
        return in_array($pendingRequest->method(), [Method::POST, Method::PATCH, Method::DELETE]);
    }
}