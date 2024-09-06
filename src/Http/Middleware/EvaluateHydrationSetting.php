<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Http\PendingRequest;

class EvaluateHydrationSetting
{
    public function __invoke(PendingRequest $pendingRequest): void
    {
        $pendingRequest->getConnector()->evaluateHydrationSetting();
    }
}
