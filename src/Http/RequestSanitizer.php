<?php

namespace Mollie\Api\Http;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Repositories\JsonPayloadRepository;
use ReflectionProperty;

class RequestSanitizer
{
    /**
     * List of sensitive headers that should be removed when debugging
     */
    protected array $sensitiveHeaders = [
        'authorization',
        'User-Agent',
        'X-Mollie-Client-Info',
    ];

    /**
     * Remove sensitive data from a request exception
     *
     * @return RequestException The sanitized exception
     */
    public function sanitize(RequestException $exception): RequestException
    {
        $response = $exception->getResponse();
        $pendingRequest = $response->getPendingRequest();
        $request = $response->getPsrRequest();

        // Clear any sensitive payload data
        $pendingRequest->setPayload(new JsonPayloadRepository);

        // Remove sensitive headers and create a new sanitized request
        foreach ($this->sensitiveHeaders as $header) {
            if ($request->hasHeader($header)) {
                $request = $request->withoutHeader($header);
            }
        }

        // Update the PSR request in the response using reflection since it's protected
        $reflectionResponse = new ReflectionProperty($response, 'psrRequest');
        $reflectionResponse->setAccessible(true);
        $reflectionResponse->setValue($response, $request);

        return $exception;
    }
}
