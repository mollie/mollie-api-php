<?php

namespace Mollie\Api\Exceptions;

use DateTime;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ApiException extends \Exception
{
    protected ?string $field = null;

    protected string $plainMessage;

    protected ?RequestInterface $request;

    protected ?ResponseInterface $response;

    /**
     * ISO8601 representation of the moment this exception was thrown
     */
    protected \DateTimeImmutable $raisedAt;

    protected array $links = [];

    /**
     * @throws ApiException
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?string $field = null,
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
        ?Throwable $previous = null
    ) {
        $this->plainMessage = $message;

        $this->raisedAt = new \DateTimeImmutable;

        $formattedRaisedAt = $this->raisedAt->format(DateTime::ATOM);
        $message = "[{$formattedRaisedAt}] ".$message;

        if (! empty($field)) {
            $this->field = (string) $field;
            $message .= ". Field: {$this->field}";
        }

        if (! empty($response)) {
            $this->response = $response;

            $object = static::parseResponseBody($this->response);

            if (isset($object->_links)) {
                foreach ($object->_links as $key => $value) {
                    $this->links[$key] = $value;
                }
            }
        }

        if ($this->hasLink('documentation')) {
            $message .= ". Documentation: {$this->getDocumentationUrl()}";
        }

        $this->request = $request;
        if ($request) {
            $requestBody = $request->getBody()->__toString();

            if ($requestBody) {
                $message .= ". Request body: {$requestBody}";
            }
        }

        parent::__construct($message, $code, $previous);
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function getDocumentationUrl(): ?string
    {
        return $this->getUrl('documentation');
    }

    public function getDashboardUrl(): ?string
    {
        return $this->getUrl('dashboard');
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function hasResponse(): bool
    {
        return (bool) $this->response;
    }

    /**
     * @param  string  $key
     */
    public function hasLink($key): bool
    {
        return array_key_exists($key, $this->links);
    }

    /**
     * @param  string  $key
     */
    public function getLink($key): ?\stdClass
    {
        if ($this->hasLink($key)) {
            return $this->links[$key];
        }

        return null;
    }

    /**
     * @param  string  $key
     */
    public function getUrl($key): ?string
    {
        if ($this->hasLink($key)) {
            return $this->getLink($key)->href;
        }

        return null;
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    /**
     * Get the ISO8601 representation of the moment this exception was thrown
     */
    public function getRaisedAt(): \DateTimeImmutable
    {
        return $this->raisedAt;
    }

    /**
     * @param  ResponseInterface  $response
     *
     * @throws ApiException
     */
    protected static function parseResponseBody($response): \stdClass
    {
        $body = (string) $response->getBody();

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new self("Unable to decode Mollie response: '{$body}'.");
        }

        return $object;
    }

    /**
     * Retrieve the plain exception message.
     */
    public function getPlainMessage(): string
    {
        return $this->plainMessage;
    }
}
