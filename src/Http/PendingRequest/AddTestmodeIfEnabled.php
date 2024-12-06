<?php

namespace Mollie\Api\Http\PendingRequest;

use Mollie\Api\Http\PendingRequest;

class AddTestmodeIfEnabled
{
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $connector = $pendingRequest->getConnector();

        if ($connector->getTestmode() || $pendingRequest->getRequest()->getTestmode()) {
            $pendingRequest->setTestmode(true);
        }

        return $pendingRequest;
    }
}
