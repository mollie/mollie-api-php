<?php

namespace Mollie\Api\Contracts;

use Mollie\Api\Http\PendingRequest;

interface RequestMiddleware
{
    /**
     * @return PendingRequest|void
     */
    public function __invoke(PendingRequest $pendingRequest);
}
