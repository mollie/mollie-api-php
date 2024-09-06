<?php

namespace Mollie\Api\Http\PendingRequest;

use Mollie\Api\Helpers\Validator;
use Mollie\Api\Http\PendingRequest;

class ValidateProperties
{
    public function __invoke(PendingRequest $pendingRequest): PendingRequest
    {
        $request = $pendingRequest->getRequest();

        Validator::make()->validate(
            $request,
            $pendingRequest->query()->all()
        );

        return $pendingRequest;
    }
}
