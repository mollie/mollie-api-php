<?php

namespace Mollie\Api\Http\Adapter;

use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Exceptions\NetworkRequestException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Traits\HasDefaultFactories;
use Throwable;

final class CurlMollieHttpAdapter implements HttpAdapterContract
{
    use HasDefaultFactories;

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function sendRequest(PendingRequest $pendingRequest): Response
    {
        [$headers, $body, $statusCode] = $this->send($pendingRequest);

        return $this->createResponse($pendingRequest, $statusCode, $headers, $body);
    }

    /**
     * @return array{0: array<string, string>, 1: string, 2: int}
     *
     * @throws NetworkRequestException
     */
    protected function send(PendingRequest $pendingRequest): array
    {
        $curl = null;
        $request = $pendingRequest->createPsrRequest();

        try {
            $curl = CurlFactory::new($request->getUri(), $pendingRequest)
                ->withHeaders($pendingRequest->headers()->all())
                ->withMethod($pendingRequest->method(), $request->getBody())
                ->create();

            $response = curl_exec($curl);

            if ($response === false) {
                $curlErrorNumber = curl_errno($curl);
                $curlErrorMessage = 'Curl error: '.curl_error($curl);

                throw CurlErrorMapper::toException($curlErrorNumber, $curlErrorMessage, $pendingRequest);
            }

            return $this->extractResponseDetails($curl, $response);
        } finally {
            if ($curl !== null && PHP_MAJOR_VERSION < 8) {
                curl_close($curl);
            }
        }
    }

    protected function createResponse(PendingRequest $pendingRequest, int $statusCode, $headers = [], $body = null, ?Throwable $error = null): Response
    {
        $factoryCollection = $pendingRequest->getFactoryCollection();
        $responseFactory = $factoryCollection->responseFactory;

        $response = $responseFactory->createResponse($statusCode)
            ->withBody($factoryCollection->streamFactory->createStream($body));

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return new Response(
            $response,
            $pendingRequest->createPsrRequest(),
            $pendingRequest,
            $error
        );
    }

    /**
     * @return array{0: array<string, string>, 1: string, 2: int}
     */
    private function extractResponseDetails($curl, string $response): array
    {
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headerValues = substr($response, 0, $headerSize);
        $content = substr($response, $headerSize);
        $statusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        $headers = [];
        $headerLines = explode("\r\n", $headerValues);
        foreach ($headerLines as $headerLine) {
            if (strpos($headerLine, ':') !== false) {
                [$key, $value] = explode(': ', $headerLine, 2);
                $headers[$key] = $value;
            }
        }

        return [$headers, $content, $statusCode];
    }

    /**
     * The version number for the underlying http client, if available.
     *
     * @example Guzzle/6.3
     */
    public function version(): string
    {
        return 'Curl/*';
    }
}
