<?php

namespace Mollie\Api\Http\Adapter;

use Composer\CaBundle\CaBundle;
use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\CurlConnectTimeoutException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Traits\HasDefaultFactories;
use Mollie\Api\Types\Method;
use Throwable;

final class CurlMollieHttpAdapter implements HttpAdapterContract
{
    use HasDefaultFactories;

    /**
     * Default response timeout (in seconds).
     */
    public const DEFAULT_TIMEOUT = 10;

    /**
     * Default connect timeout (in seconds).
     */
    public const DEFAULT_CONNECT_TIMEOUT = 2;

    /**
     * The maximum number of retries
     */
    public const MAX_RETRIES = 5;

    /**
     * The amount of milliseconds the delay is being increased with on each retry.
     */
    public const DELAY_INCREASE_MS = 1000;

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     * @throws \Mollie\Api\Exceptions\CurlConnectTimeoutException
     */
    public function sendRequest(PendingRequest $pendingRequest): Response
    {
        for ($i = 0; $i <= self::MAX_RETRIES; $i++) {
            usleep($i * self::DELAY_INCREASE_MS);

            try {
                [$headers, $body, $statusCode] = $this->send($pendingRequest);

                return $this->createResponse($pendingRequest, $statusCode, $headers, $body);
            } catch (CurlConnectTimeoutException $e) {
                return $this->createResponse($pendingRequest, 504, [], null, $e);
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
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    protected function send(PendingRequest $pendingRequest): array
    {
        $request = $pendingRequest->createPsrRequest();

        $curl = $this->initializeCurl($request->getUri());
        $this->setCurlHeaders($curl, $pendingRequest->headers()->all());
        $this->setCurlMethodOptions($curl, $pendingRequest->method(), $request->getBody());

        $startTime = microtime(true);
        $response = curl_exec($curl);
        $endTime = microtime(true);

        if ($response === false) {
            $this->handleCurlError($curl, $endTime - $startTime);
        }

        [$headers, $content, $statusCode] = $this->extractResponseDetails($curl, $response);
        curl_close($curl);

        return [$headers, $content, $statusCode];
    }

    private function initializeCurl(string $url)
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::DEFAULT_CONNECT_TIMEOUT);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::DEFAULT_TIMEOUT);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_CAINFO, CaBundle::getBundledCaBundlePath());

        return $curl;
    }

    private function setCurlHeaders($curl, array $headers)
    {
        $headers['Content-Type'] = 'application/json';

        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->parseHeaders($headers));
    }

    private function setCurlMethodOptions($curl, string $method, ?string $body): void
    {
        switch ($method) {
            case Method::POST:
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                break;

            case Method::PATCH:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, Method::PATCH);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                break;

            case Method::DELETE:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, Method::DELETE);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                break;

            case Method::GET:
            default:
                if ($method !== Method::GET) {
                    throw new \InvalidArgumentException('Invalid HTTP method: '.$method);
                }

                break;
        }
    }

    private function handleCurlError($curl, float $executionTime): void
    {
        $curlErrorNumber = curl_errno($curl);
        $curlErrorMessage = 'Curl error: '.curl_error($curl);

        if ($this->isConnectTimeoutError($curlErrorNumber, $executionTime)) {
            throw new CurlConnectTimeoutException('Unable to connect to Mollie. '.$curlErrorMessage);
        }

        throw new ApiException($curlErrorMessage);
    }

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
     * @param  string|float  $executionTime
     */
    protected function isConnectTimeoutError(int $curlErrorNumber, $executionTime): bool
    {
        $connectErrors = [
            \CURLE_COULDNT_RESOLVE_HOST => true,
            \CURLE_COULDNT_CONNECT => true,
            \CURLE_SSL_CONNECT_ERROR => true,
            \CURLE_GOT_NOTHING => true,
        ];

        if (isset($connectErrors[$curlErrorNumber])) {
            return true;
        }

        if ($curlErrorNumber === \CURLE_OPERATION_TIMEOUTED) {
            if ($executionTime > self::DEFAULT_TIMEOUT) {
                return false;
            }

            return true;
        }

        return false;
    }

    private function parseHeaders(array $headers): array
    {
        $result = [];

        foreach ($headers as $key => $value) {
            $result[] = $key.': '.$value;
        }

        return $result;
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
