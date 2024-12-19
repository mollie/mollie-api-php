<?php

namespace Mollie\Api\Fake;

use Mollie\Api\Traits\HasDefaultFactories;
use Mollie\Api\Utils\Arr;
use PHPUnit\Framework\Assert as PHPUnit;
use Psr\Http\Message\ResponseInterface;

class MockResponse
{
    use HasDefaultFactories;

    private int $status;

    private string $resourceKey;

    private string $body;

    /**
     * @param string|array $body
     * @param integer $status
     * @param string $resourcekey
     */
    public function __construct(
        $body,
        int $status = 200,
        string $resourcekey = ''
    ) {
        $this->body = is_array($body) ? json_encode($body) : $body;
        $this->status = $status;
        $this->resourceKey = $resourcekey;
    }

    public static function ok(string $body = '', string $resourceKey = ''): self
    {
        return new self($body, 200, $resourceKey);
    }

    public static function created(string $body = '', string $resourceKey = ''): self
    {
        return new self($body, 201, $resourceKey);
    }

    public static function noContent(string $body = '', string $resourceKey = ''): self
    {
        return new self($body, 204, $resourceKey);
    }

    public static function notFound(string $resourceKey = ''): self
    {
        return new self('', 404, $resourceKey);
    }

    public static function unprocessableEntity(string $body = '', string $resourceKey = ''): self
    {
        return new self($body, 422, $resourceKey);
    }

    public function createPsrResponse(): ResponseInterface
    {
        $psrResponse = $this
            ->factories()
            ->responseFactory
            ->createResponse($this->status);

        $body = $this
            ->factories()
            ->streamFactory
            ->createStream($this->body());

        return $psrResponse->withBody($body);
    }

    public function body(): string
    {
        if (empty($body = $this->body)) {
            return '';
        }

        if ($this->isJson($body)) {
            return $body;
        }

        $path = Arr::join([
            __DIR__,
            'Responses',
            $body.'.json',
        ], DIRECTORY_SEPARATOR);

        $contents = file_get_contents($path);

        if (! empty($this->resourceKey)) {
            $contents = str_replace('{{ RESOURCE_ID }}', $this->resourceKey, $contents);
        }

        return $contents;
    }

    public function assertResponseBodyEquals(ResponseInterface $response): void
    {
        $body = $response->getBody();
        $body->rewind();

        PHPUnit::assertEquals(
            $body->getContents(),
            $this->createPsrResponse()->getBody()->getContents(),
            'Response does not match'
        );
    }

    private function isJson($string): bool
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }
}
