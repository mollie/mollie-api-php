<?php

namespace Mollie\Api\HttpAdapter;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions as GuzzleRequestOptions;
use Mollie\Api\Exceptions\ApiException;
use Psr\Http\Message\ResponseInterface;

final class Guzzle6And7MollieHttpAdapter implements MollieHttpAdapterInterface
{
    /**
     * Default response timeout (in seconds).
     */
    const DEFAULT_TIMEOUT = 10;

    /**
     * Default connect timeout (in seconds).
     */
    const DEFAULT_CONNECT_TIMEOUT = 2;

    /**
     * HTTP status code for an empty ok response.
     */
    const HTTP_NO_CONTENT = 204;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Instantiate a default adapter with sane configuration for Guzzle 6 or 7.
     *
     * @return static
     */
    public static function createDefault()
    {
        $retryMiddlewareFactory = new Guzzle6And7RetryMiddlewareFactory;
        $handlerStack = HandlerStack::create();
        $handlerStack->push($retryMiddlewareFactory->retry());

        $client = new Client([
            GuzzleRequestOptions::VERIFY => CaBundle::getBundledCaBundlePath(),
            GuzzleRequestOptions::TIMEOUT => self::DEFAULT_TIMEOUT,
            GuzzleRequestOptions::CONNECT_TIMEOUT => self::DEFAULT_CONNECT_TIMEOUT,
            'handler' => $handlerStack,
        ]);

        return new Guzzle6And7MollieHttpAdapter($client);
    }

    /**
     * Send a request to the specified Mollie api url.
     *
     * @param $httpMethod
     * @param $url
     * @param $headers
     * @param $httpBody
     * @return \stdClass|null
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function send($httpMethod, $url, $headers, $httpBody)
    {
        $request = new Request($httpMethod, $url, $headers, $httpBody);

        try {
            $response = $this->httpClient->send($request, ['http_errors' => false]);
        } catch (GuzzleException $e) {

            // Not all Guzzle Exceptions implement hasResponse() / getResponse()
            if (method_exists($e, 'hasResponse') && method_exists($e, 'getResponse')) {
                if ($e->hasResponse()) {
                    throw ApiException::createFromResponse($e->getResponse(), $request);
                }
            }

            throw new ApiException($e->getMessage(), $e->getCode(), null, $request, null);
        }

        if (! $response) {
            throw new ApiException("Did not receive API response.", 0, null, $request);
        }

        return $this->parseResponseBody($response);
    }

    /**
     * Parse the PSR-7 Response body
     *
     * @param ResponseInterface $response
     * @return \stdClass|null
     * @throws ApiException
     */
    private function parseResponseBody(ResponseInterface $response)
    {
        $body = (string) $response->getBody();
        if (empty($body)) {
            if ($response->getStatusCode() === self::HTTP_NO_CONTENT) {
                return null;
            }

            throw new ApiException("No response body found.");
        }

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException("Unable to decode Mollie response: '{$body}'.");
        }

        if ($response->getStatusCode() >= 400) {
            throw ApiException::createFromResponse($response, null);
        }

        return $object;
    }

    /**
     * The version number for the underlying http client, if available. This is used to report the UserAgent to Mollie,
     * for convenient support.
     * @example Guzzle/6.3
     *
     * @return string|null
     */
    public function versionString()
    {
        if (defined('\GuzzleHttp\ClientInterface::MAJOR_VERSION')) { // Guzzle 7
            return "Guzzle/" . ClientInterface::MAJOR_VERSION;
        } elseif (defined('\GuzzleHttp\ClientInterface::VERSION')) { // Before Guzzle 7
            return "Guzzle/" . ClientInterface::VERSION;
        }

        return null;
    }
}
