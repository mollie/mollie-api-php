<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\ResponseContract;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class PsrResponseHandler
{
    private ResponseHandler $responseHandler;

    public function __construct(ResponseHandler $responseHandler)
    {
        $this->responseHandler = $responseHandler;
    }

    public static function create(): self
    {
        return new self(new ResponseHandler());
    }

    /**
     * Undocumented function
     *
     * @param mixed|callback|ResponseContract|null $response
     * @param int $code
     * @param string|null $requestBody
     * @return ResponseContract
     */
    public function handle(?ResponseInterface $response = null, int $code, ?string $requestBody = null): ResponseContract
    {
        if ($response === null) {
            return ResponseHandler::noResponse();
        }

        if (!$response instanceof ResponseInterface) {
            throw new RuntimeException("Response must be an instance of ResponseInterface.");
        }

        $body = (string) $response->getBody();

        return $this->responseHandler->handle(
            new Response(
                $code,
                $response->getHeaders(),
                $body
            ),
            $requestBody
        );
    }
}
