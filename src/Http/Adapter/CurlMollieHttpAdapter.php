<?php

namespace Mollie\Api\Http\Adapter;

use Composer\CaBundle\CaBundle;
use CurlHandle;
use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Http\Adapter\CurlConnectionErrorException;
use Mollie\Api\Http\Adapter\CurlException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;
use Mollie\Api\Traits\HasDefaultFactories;
use Mollie\Api\Types\Method;
use Throwable;
use Psr\Http\Message\RequestInterface;

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
     */
    public function sendRequest(PendingRequest $pendingRequest): Response
    {
        $lastException = null;
        for ($i = 0; $i <= self::MAX_RETRIES; $i++) {
            usleep($i * self::DELAY_INCREASE_MS);

            try {
                [$headers, $body, $statusCode] = $this->send($pendingRequest);

                return $this->createResponse($pendingRequest, $statusCode, $headers, $body);
            } catch (CurlConnectionErrorException $e) {
                // Connection errors are fatal and shouldn't be retried
                $lastException = $e;
            } catch (CurlException $e) {
                // Only retry non-connection CURL errors
                $lastException = $e;
            }
        }

        return $this->createResponse($pendingRequest, ResponseStatusCode::HTTP_GATEWAY_TIMEOUT, [], null, $lastException);
    }

    /**
     * @throws CurlException
     */
    protected function send(PendingRequest $pendingRequest): array
    {
        $curl = null;
        $request = $pendingRequest->createPsrRequest();

        try {
            $curl = $this->initializeCurl($request->getUri());

            $this->setCurlHeaders($curl, $pendingRequest->headers()->all());
            $this->setCurlMethodOptions($curl, $pendingRequest->method(), $request->getBody());

            $startTime = microtime(true);
            $response = curl_exec($curl);
            $endTime = microtime(true);

            if ($response === false) {
                $this->handleCurlError($curl, $endTime - $startTime, $request);
            }

            return $this->extractResponseDetails($curl, $response);
        } finally {
            if ($curl !== null) {
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

    private function initializeCurl(string $url): CurlHandle
    {
        $curl = curl_init($url);
        if ($curl === false) {
            throw new CurlException('Failed to initialize CURL');
        }

        $this->setCurlOption($curl, CURLOPT_RETURNTRANSFER, true);
        $this->setCurlOption($curl, CURLOPT_HEADER, true);
        $this->setCurlOption($curl, CURLOPT_CONNECTTIMEOUT, self::DEFAULT_CONNECT_TIMEOUT);
        $this->setCurlOption($curl, CURLOPT_TIMEOUT, self::DEFAULT_TIMEOUT);
        $this->setCurlOption($curl, CURLOPT_SSL_VERIFYPEER, true);
        $this->setCurlOption($curl, CURLOPT_CAINFO, CaBundle::getBundledCaBundlePath());

        return $curl;
    }

    private function setCurlOption(CurlHandle $curl, int $option, $value): void
    {
        if (curl_setopt($curl, $option, $value) === false) {
            throw new CurlException(
                sprintf('Failed to set CURL option %d', $option),
                curl_errno($curl),
            );
        }
    }

    private function setCurlHeaders(CurlHandle $curl, array $headers): void
    {
        $headers['Content-Type'] = 'application/json';

        -$this->setCurlOption($curl, CURLOPT_HTTPHEADER, $this->parseHeaders($headers));
    }

    private function setCurlMethodOptions(CurlHandle $curl, string $method, ?string $body): void
    {
        switch ($method) {
            case Method::POST:
                $this->setCurlOption($curl, CURLOPT_POST, true);
                $this->setCurlOption($curl, CURLOPT_POSTFIELDS, $body);
                break;

            case Method::PATCH:
                $this->setCurlOption($curl, CURLOPT_CUSTOMREQUEST, Method::PATCH);
                $this->setCurlOption($curl, CURLOPT_POSTFIELDS, $body);
                break;

            case Method::DELETE:
                $this->setCurlOption($curl, CURLOPT_CUSTOMREQUEST, Method::DELETE);
                $this->setCurlOption($curl, CURLOPT_POSTFIELDS, $body);
                break;

            case Method::GET:
            default:
                if ($method !== Method::GET) {
                    throw new \InvalidArgumentException('Invalid HTTP method: ' . $method);
                }
                break;
        }
    }

    /**
     * @throws CurlException
     * @return never
     */
    private function handleCurlError(CurlHandle $curl, float $executionTime, RequestInterface $request): void
    {
        $curlErrorNumber = curl_errno($curl);
        $curlErrorMessage = 'Curl error: ' . curl_error($curl);

        throw $this->mapCurlErrorToException($curlErrorNumber, $curlErrorMessage, $request, $executionTime);
    }

    private function mapCurlErrorToException(int $curlErrorNumber, string $curlErrorMessage, RequestInterface $request, float $executionTime): CurlException
    {
        static $messages = [
            \CURLE_UNSUPPORTED_PROTOCOL => 'Unsupported protocol. Please check the URL.',
            \CURLE_URL_MALFORMAT => 'Malformed URL. Please check the URL format.',
            \CURLE_COULDNT_RESOLVE_PROXY => 'Could not resolve proxy. Please check your proxy settings.',
            \CURLE_COULDNT_RESOLVE_HOST => 'Could not resolve host. Please check your internet connection and DNS settings.',
            \CURLE_COULDNT_CONNECT => 'Could not connect to host. Please check if the service is available.',
            \CURLE_FTP_ACCESS_DENIED => 'Remote access denied. Please check your authentication credentials.',
            \CURLE_OUT_OF_MEMORY => 'Out of memory while processing the request.',
            \CURLE_OPERATION_TIMEOUTED => 'Operation timed out. The request took too long to complete.',
            \CURLE_SSL_CONNECT_ERROR => 'SSL connection error. Please check your SSL/TLS configuration.',
            \CURLE_GOT_NOTHING => 'Server returned nothing. Empty response received.',
            \CURLE_SSL_CERTPROBLEM => 'Problem with the local SSL certificate.',
            \CURLE_SSL_CIPHER => 'Problem with the SSL cipher.',
            \CURLE_SSL_CACERT => 'Problem with the SSL CA cert.',
            \CURLE_BAD_CONTENT_ENCODING => 'Unrecognized content encoding.',
        ];

        $message = $messages[$curlErrorNumber] ?? 'An error occurred while making the request.';
        $message .= ' ' . $curlErrorMessage;

        if ($this->isConnectionError($curlErrorNumber, $executionTime)) {
            return new CurlConnectionErrorException($message, $curlErrorNumber, $request);
        }

        return new CurlException($message, $curlErrorNumber);
    }

    private function isConnectionError(int $curlErrorNumber, float $executionTime): bool
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
            // Only treat it as a connection error if it timed out during the connection phase
            return $executionTime <= self::DEFAULT_CONNECT_TIMEOUT;
        }

        return false;
    }

    private function extractResponseDetails(CurlHandle $curl, string $response): array
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

    private function parseHeaders(array $headers): array
    {
        $result = [];

        foreach ($headers as $key => $value) {
            $result[] = $key . ': ' . $value;
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
