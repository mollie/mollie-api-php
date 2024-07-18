<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\ResponseContract;
use Mollie\Api\Exceptions\ApiException;

class Response implements ResponseContract
{
    use HasHttpPhrases;

    private int $statusCode;

    private ?string $body = null;

    private string $reasonPhrase;

    /**
     * The decoded JSON response.
     *
     * @var \stdClass
     */
    protected ?\stdClass $decoded = null;

    public function __construct(
        int $statusCode = 200,
        ?string $body = null,
        string $reasonPhrase = ''
    ) {
        $this->statusCode = $statusCode;
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
    public function decode(): \stdClass
    {
        if (empty($body = $this->body())) {
            return (object)[];
        }

        if (! $this->decoded) {
            $this->decoded = @json_decode($body);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ApiException("Unable to decode Mollie response: '{$body}'.");
            }
        }

        return $this->decoded;
    }

    public function status(): int
    {
        return $this->statusCode;
    }

    public function isEmpty(): bool
    {
        return empty($this->body());
    }

    public function getReasonPhrase(): string
    {
        if (empty($this->reasonPhrase) && isset(static::$phrases[$this->statusCode])) {
            return static::$phrases[$this->statusCode];
        }

        return $this->reasonPhrase;
    }
}
