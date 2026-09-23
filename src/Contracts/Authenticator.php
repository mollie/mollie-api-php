<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

use Mollie\Api\Http\PendingRequest;

interface Authenticator
{
    public function authenticate(PendingRequest $pendingRequest): void;
}
