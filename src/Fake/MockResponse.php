<?php

namespace Mollie\Api\Fake;

use Mollie\Api\Traits\HasDefaultFactories;
use Mollie\Api\Utils\Arr;
use Psr\Http\Message\ResponseInterface;

class MockResponse
{
    use HasDefaultFactories;

    private int $status;

    private string $resourceKey;

    private string $body;

    /**
     * @param  string|array|callable  $body
     */
    public function __construct(
        $body,
        int $status = 200,
        string $resourceKey = ''
    ) {
        $this->body = is_array($body) ? json_encode($body) : $body;
        $this->status = $status;
        $this->resourceKey = $resourceKey;
    }

    /**
     * @param  string|array  $body
     */
    public static function ok($body = [], string $resourceKey = ''): self
    {
        return new self($body, 200, $resourceKey);
    }

    /**
     * @param  string|array  $body
     */
    public static function created($body = [], string $resourceKey = ''): self
    {
        return new self($body, 201, $resourceKey);
    }

    /**
     * @param  string|array  $body
     */
    public static function noContent($body = [], string $resourceKey = ''): self
    {
        return new self($body, 204, $resourceKey);
    }

    public static function notFound(string $resourceKey = ''): self
    {
        return new self('', 404, $resourceKey);
    }

    /**
     * @param  string|array  $body
     */
    public static function unprocessableEntity($body = [], string $resourceKey = ''): self
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

    private function isJson($string): bool
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }
}
