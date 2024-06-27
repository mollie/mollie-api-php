<?php

namespace Mollie\Api\Http\Adapter;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions as GuzzleRequestOptions;
use Mollie\Api\Contracts\MollieHttpAdapterContract;
use Mollie\Api\Contracts\ResponseContract;
use Mollie\Api\Contracts\SupportsDebuggingContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\IsDebuggable;
use Mollie\Api\Http\PsrResponseHandler;

final class GuzzleMollieHttpAdapter implements MollieHttpAdapterContract, SupportsDebuggingContract
{
    use IsDebuggable;

    /**
     * Default response timeout (in seconds).
     */
    public const DEFAULT_TIMEOUT = 10;

    /**
     * Default connect timeout (in seconds).
     */
    public const DEFAULT_CONNECT_TIMEOUT = 2;

    /**
     * HTTP status code for an empty ok response.
     */
    public const HTTP_NO_CONTENT = 204;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected ClientInterface $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Instantiate a default adapter with sane configuration for Guzzle 6 or 7.
     *
     * @return self
     */
    public static function createDefault(): self
    {
        $retryMiddlewareFactory = new GuzzleRetryMiddlewareFactory;
        $handlerStack = HandlerStack::create();
        $handlerStack->push($retryMiddlewareFactory->retry());

        $client = new Client([
            GuzzleRequestOptions::VERIFY => CaBundle::getBundledCaBundlePath(),
            GuzzleRequestOptions::TIMEOUT => self::DEFAULT_TIMEOUT,
            GuzzleRequestOptions::CONNECT_TIMEOUT => self::DEFAULT_CONNECT_TIMEOUT,
            'handler' => $handlerStack,
        ]);

        return new GuzzleMollieHttpAdapter($client);
    }

    /**
     * Send a request to the specified Mollie api url.
     *
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param string $body
     * @return ResponseContract
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function send(string $method, string $url, $headers, ?string $body = null): ResponseContract
    {
        $request = new Request($method, $url, $headers, $body);

        try {
            $response = $this->httpClient->send($request, ['http_errors' => false]);
        } catch (GuzzleException $e) {
            // Prevent sensitive request data from ending up in exception logs unintended
            if (! $this->debug) {
                $request = null;
            }

            // Not all Guzzle Exceptions implement hasResponse() / getResponse()
            if (method_exists($e, 'hasResponse') && method_exists($e, 'getResponse')) {
                if ($e->hasResponse()) {
                    throw ApiException::createFromResponse($e->getResponse(), $request);
                }
            }

            throw new ApiException($e->getMessage(), $e->getCode(), null, $request, null);
        }

        return PsrResponseHandler::create()
            ->handle($request, $response, $response->getStatusCode(), $body);
    }

    /**
     * The version number for the underlying http client, if available.
     * This is used to report the UserAgent to Mollie, for convenient support.
     *
     * @example Guzzle/7.0
     *
     * @return string
     */
    public function version(): string
    {
        return "Guzzle/" . ClientInterface::MAJOR_VERSION;
    }
}
