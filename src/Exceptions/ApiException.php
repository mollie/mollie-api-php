<?php

namespace Mollie\Api\Exceptions;

use DateTimeImmutable;
use Mollie\Api\Http\Response;
use Throwable;

/**
 * Exception thrown when Mollie's API returns an error response.
 * This exception always has both a request and a response, where the response
 * contains error details from Mollie's API.
 */
class ApiException extends RequestException
{
    protected string $plainMessage;

    protected \DateTimeImmutable $raisedAt;

    /** @var array<string, \stdClass> */
    protected array $links = [];

    /**
     * @param  Response  $response  The response that caused this exception
     * @param  string  $message  The error message
     * @param  int  $code  The error code
     * @param  Throwable|null  $previous  Previous exception if any
     *
     * @throws ApiException
     */
    public function __construct(
        Response $response,
        string $message,
        int $code,
        ?Throwable $previous = null
    ) {
        $this->plainMessage = $message;
        $this->raisedAt = new DateTimeImmutable;

        $formattedRaisedAt = $this->raisedAt->format(DateTimeImmutable::ATOM);
        $message = "[{$formattedRaisedAt}] ".$message;

        $object = $response->json();
        if (isset($object->_links)) {
            foreach ($object->_links as $key => $value) {
                $this->links[$key] = $value;
            }
        }

        if ($this->hasLink('documentation')) {
            $message .= ". Documentation: {$this->getDocumentationUrl()}";
        }

        if ($requestBody = $response->getPsrRequest()->getBody()->__toString()) {
            $message .= ". Request body: {$requestBody}";
        }

        parent::__construct($response, $message, $code, $previous);
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();
        $status = $response->status();

        return new self(
            $response,
            sprintf(
                'Error executing API call (%d: %s): %s',
                $status,
                $body->title ?? 'Unknown',
                $body->detail ?? 'Unknown'
            ),
            $status,
            null
        );
    }

    public function getDocumentationUrl(): ?string
    {
        return $this->getUrl('documentation');
    }

    public function getDashboardUrl(): ?string
    {
        return $this->getUrl('dashboard');
    }

    public function hasLink(string $key): bool
    {
        return array_key_exists($key, $this->links);
    }

    public function getLink(string $key): \stdClass
    {
        if ($this->hasLink($key)) {
            return $this->links[$key];
        }

        throw new \RuntimeException("Link '{$key}' not found");
    }

    public function getUrl(string $key): ?string
    {
        if (! $this->hasLink($key)) {
            return null;
        }

        $link = $this->getLink($key);

        return $link->href;
    }

    /**
     * Get the ISO8601 representation of the moment this exception was thrown
     */
    public function getRaisedAt(): DateTimeImmutable
    {
        return $this->raisedAt;
    }

    /**
     * Retrieve the plain exception message without timestamp and metadata.
     */
    public function getPlainMessage(): string
    {
        return $this->plainMessage;
    }
}
