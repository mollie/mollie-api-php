<?php

namespace Tests\Mollie\TestHelpers;

use Mollie\Api\HttpAdapter\MollieHttpAdapterInterface;

class FakeHttpAdapter implements MollieHttpAdapterInterface
{
    /**
     * @var \stdClass|null
     */
    private $response;

    /**
     * @var string
     */
    private $usedMethod;

    /**
     * @var string
     */
    private $usedUrl;

    /**
     * @var string
     */
    private $usedHeaders;

    /**
     * @var string
     */
    private $usedBody;

    /**
     * FakeHttpAdapter constructor.
     * @param \stdClass|null|\GuzzleHttp\Psr7\Response $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @param string $meethod
     * @param string $url
     * @param string $headers
     * @param string $body
     * @return \stdClass|void|null
     */
    public function send(string $meethod, string $url, $headers, ?string $body): ?\stdClass
    {
        $this->usedMethod = $meethod;
        $this->usedUrl = $url;
        $this->usedHeaders = $headers;
        $this->usedBody = $body;

        return $this->response;
    }

    /**
     * @return string
     */
    public function versionString(): string
    {
        return 'fake';
    }

    /**
     * @return mixed
     */
    public function getUsedMethod()
    {
        return $this->usedMethod;
    }

    /**
     * @return mixed
     */
    public function getUsedUrl()
    {
        return $this->usedUrl;
    }

    /**
     * @return mixed
     */
    public function getUsedHeaders()
    {
        return $this->usedHeaders;
    }

    /**
     * @return mixed
     */
    public function getUsedBody()
    {
        return $this->usedBody;
    }
}
