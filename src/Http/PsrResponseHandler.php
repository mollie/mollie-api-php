<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\ResponseContract;
use Mollie\Api\Exceptions\ApiException;
use Psr\Http\Message\RequestInterface;
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
     * @param RequestInterface|null $psrResponse
     * @param ResponseInterface|null $psrResponse
     * @param int $code
     * @param string|null $requestBody
     * @return ResponseContract
     */
    public function handle(
        ?RequestInterface $prsRequest = null,
        ?ResponseInterface $psrResponse = null,
        int $code = 200,
        ?string $requestBody = null
    ): ResponseContract {
        if ($psrResponse === null) {
            return ResponseHandler::noResponse();
        }

        if (! $psrResponse instanceof ResponseInterface) {
            throw new RuntimeException("Response must be an instance of ResponseInterface.");
        }

        $body = (string) $psrResponse->getBody();

        $response = new Response(
            $code,
            $body
        );

        $this->responseHandler->guard($response);

        try {
            $this->responseHandler->throwExceptionIfRequestFailed($response, $requestBody);
        } catch (ApiException $e) {
            throw new ApiException(
                $e->getMessage(),
                $e->getCode(),
                $e->getField(),
                $prsRequest,
                $psrResponse,
                $e->getPrevious()
            );
        }

        return $response;
    }
}
