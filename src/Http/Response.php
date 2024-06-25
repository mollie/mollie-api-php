<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\ResponseContract;
use Mollie\Api\Exceptions\ApiException;

class Response implements ResponseContract
{
    use HasHttpPhrases;

    private int $statusCode;

    private array $headers;

    private ?string $body = null;

    private string $reasonPhrase;

    /**
     * The decoded JSON response.
     *
     * @var \stdClass
     */
    protected ?\stdClass $decoded = null;

    /**
     * HTTP status code for an empty ok response.
     */
    public const HTTP_NO_CONTENT = 204;

    public function __construct(
        int $statusCode = 200,
        array $headers = [],
        ?string $body = null,
        string $reasonPhrase = ''
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * Get the body of the response.
     *
     * @return string
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * Get the JSON decoded body of the response as an array or scalar value.
     *
     * @return \stdClass
     */
    public function json(): \stdClass
    {
        if (!$this->decoded) {
            $this->decoded = @json_decode($this->body());

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ApiException("Unable to decode Mollie response: '{$this->body()}'.");
            }
        }

        return $this->decoded;
    }

    public function status(): int
    {
        return $this->statusCode;
    }

    public function getReasonPhrase(): string
    {
        if (empty($this->reasonPhrase) && isset(static::$phrases[$this->statusCode])) {
            return static::$phrases[$this->statusCode];
        }

        return $this->reasonPhrase;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }

    public function getHeader(string $name): array
    {
        return $this->hasHeader($name)
            ? $this->headers[$name]
            : [];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }
}
