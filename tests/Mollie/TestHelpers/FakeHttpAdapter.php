<?php

namespace Tests\Mollie\TestHelpers;

use GuzzleHttp\Psr7\Response;
use Mollie\Api\Contracts\MollieHttpAdapterContract;
use Mollie\Api\Contracts\ResponseContract;
use Mollie\Api\Http\PsrResponseHandler;

class FakeHttpAdapter implements MollieHttpAdapterContract
{
    private Response $response;

    private string $usedMethod;

    private string $usedUrl;

    private array $usedHeaders = [];

    private ?string $usedBody = null;

    /**
     * FakeHttpAdapter constructor.
     * @paramResponse $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param string $body
     * @return ResponseContract
     */
    public function send(string $method, string $url, $headers, ?string $body): ResponseContract
    {
        $this->usedMethod = $method;
        $this->usedUrl = $url;
        $this->usedHeaders = $headers;
        $this->usedBody = $body;

        return PsrResponseHandler::create()
            ->handle(null, $this->response, 200, $body);
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
