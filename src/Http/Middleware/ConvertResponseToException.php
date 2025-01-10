<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Contracts\ResponseMiddleware;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\UnauthorizedException;
use Mollie\Api\Exceptions\ForbiddenException;
use Mollie\Api\Exceptions\NotFoundException;
use Mollie\Api\Exceptions\MethodNotAllowedException;
use Mollie\Api\Exceptions\RequestTimeoutException;
use Mollie\Api\Exceptions\ServiceUnavailableException;
use Mollie\Api\Exceptions\TooManyRequestsException;
use Mollie\Api\Exceptions\ValidationException;
use Mollie\Api\Http\Response;

class ConvertResponseToException implements ResponseMiddleware
{
    public function __invoke(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $status = $response->status();

        switch ($status) {
            case 401:
                throw UnauthorizedException::fromResponse($response);
            case 403:
                throw ForbiddenException::fromResponse($response);
            case 404:
                throw NotFoundException::fromResponse($response);
            case 405:
                throw MethodNotAllowedException::fromResponse($response);
            case 408:
                throw RequestTimeoutException::fromResponse($response);
            case 422:
                throw ValidationException::fromResponse($response);
            case 429:
                throw TooManyRequestsException::fromResponse($response);
            case 503:
                throw ServiceUnavailableException::fromResponse($response);
            default:
                throw new ApiException(
                    sprintf(
                        'Error executing API call (%d: %s): %s',
                        $status,
                        $response->json()->title,
                        $response->json()->detail
                    ),
                    $status,
                    $response->getPsrRequest(),
                    $response->getPsrResponse(),
                    null
                );
        }
    }
}
