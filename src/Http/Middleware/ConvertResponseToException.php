<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Contracts\ResponseMiddleware;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\ForbiddenException;
use Mollie\Api\Exceptions\MethodNotAllowedException;
use Mollie\Api\Exceptions\NotFoundException;
use Mollie\Api\Exceptions\RequestTimeoutException;
use Mollie\Api\Exceptions\ServiceUnavailableException;
use Mollie\Api\Exceptions\TooManyRequestsException;
use Mollie\Api\Exceptions\UnauthorizedException;
use Mollie\Api\Exceptions\ValidationException;
use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;

class ConvertResponseToException implements ResponseMiddleware
{
    public function __invoke(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $status = $response->status();

        switch ($status) {
            case ResponseStatusCode::HTTP_UNAUTHORIZED:
                throw UnauthorizedException::fromResponse($response);
            case ResponseStatusCode::HTTP_FORBIDDEN:
                throw ForbiddenException::fromResponse($response);
            case ResponseStatusCode::HTTP_NOT_FOUND:
                throw NotFoundException::fromResponse($response);
            case ResponseStatusCode::HTTP_METHOD_NOT_ALLOWED:
                throw MethodNotAllowedException::fromResponse($response);
            case ResponseStatusCode::HTTP_REQUEST_TIMEOUT:
                throw RequestTimeoutException::fromResponse($response);
            case ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY:
                throw ValidationException::fromResponse($response);
            case ResponseStatusCode::HTTP_TOO_MANY_REQUESTS:
                throw TooManyRequestsException::fromResponse($response);
            case ResponseStatusCode::HTTP_SERVICE_UNAVAILABLE:
                throw ServiceUnavailableException::fromResponse($response);
            default:
                throw ApiException::fromResponse($response);
        }
    }
}
