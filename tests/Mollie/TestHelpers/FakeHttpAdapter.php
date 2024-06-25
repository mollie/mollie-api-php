<?php

namespace Tests\Mollie\TestHelpers;

use Mollie\Api\Contracts\MollieHttpAdapterContract;
use Mollie\Api\Contracts\ResponseContract;
use Mollie\Api\Http\PsrResponseHandler;

class FakeHttpAdapter implements MollieHttpAdapterContract
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
     * @param string $method
     * @param string $url
     * @param string $headers
     * @param string $body
     * @return \stdClass|null
     */
    public function send(string $method, string $url, $headers, ?string $body): ResponseContract
    {
        $this->usedMethod = $method;
        $this->usedUrl = $url;
        $this->usedHeaders = $headers;
        $this->usedBody = $body;

        return PsrResponseHandler::create()
            ->handle($this->response, 200, $body);
    }

    /**
     * @return string
     */
    public function version(): string
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
