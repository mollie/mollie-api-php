<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

use Mollie\Api\Http\PendingRequest;

interface RequestMiddleware
{
    /**
     * @return PendingRequest|void
     */
    public function __invoke(PendingRequest $pendingRequest);
}
