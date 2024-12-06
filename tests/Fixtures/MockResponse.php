<?php

namespace Tests\Fixtures;

use Mollie\Api\Helpers\Arr;
use Mollie\Api\Traits\HasDefaultFactories;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;

class MockResponse
{
    use HasDefaultFactories;

    private int $status;

    private string $resourceId;

    private string $body;

    public function __construct(
        int $status = 200,
        string $body = '',
        string $resourceId = ''
    ) {
        $this->status = $status;
        $this->resourceId = $resourceId;
        $this->body = $body;
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

        if (! empty($this->resourceId)) {
            $contents = str_replace('{{ RESOURCE_ID }}', $this->resourceId, $contents);
        }

        return $contents;
    }

    public function assertResponseBodyEquals(ResponseInterface $response): void
    {
        $body = $response->getBody();
        $body->rewind();

        Assert::assertEquals(
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
