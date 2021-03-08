<?php

namespace Mollie\Api\HttpAdapter;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions as GuzzleRequestOptions;

class Guzzle6And7MollieHttpAdapter implements MollieHttpAdapter
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
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public static function createDefault()
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

        return new static($client);
    }

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
