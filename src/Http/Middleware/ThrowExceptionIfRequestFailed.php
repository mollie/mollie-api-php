<?php

namespace Mollie\Api\Http\Middleware;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Response;

class ThrowExceptionIfRequestFailed
{
    public function __invoke(Response $response)
    {
        if ($response->successful()) {
            return;
        }

        $body = $response->json();

        $message = "Error executing API call ({$body->status}: {$body->title}): {$body->detail}";

        $field = null;

        if (! empty($body->field)) {
            $field = $body->field;
        }

        if (isset($body->_links, $body->_links->documentation)) {
            $message .= ". Documentation: {$body->_links->documentation->href}";
        }

        if ($response->getPendingRequest()->body()) {
            $streamFactory = $response
                ->getPendingRequest()
                ->getFactoryCollection()
                ->streamFactory;

            $message .= ". Request body: {$response->getPendingRequest()->body()->toStream($streamFactory)->getContents()}";
        }

        throw new ApiException(
            $message,
            $response->status(),
            $field,
            $response->getPsrRequest(),
            $response->getPsrResponse(),
            $response->getSenderException()
        );
    }
}
