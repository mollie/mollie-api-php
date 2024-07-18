<?php

namespace Mollie\Api\Exceptions;

use DateTime;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ApiException extends \Exception
{
    /**
     * @var string
     */
    protected ?string $field = null;

    /**
     * @var string
     */
    protected string $plainMessage;

    /**
     * @var RequestInterface|null
     */
    protected ?RequestInterface $request;

    /**
     * @var ResponseInterface|null
     */
    protected ?ResponseInterface $response;

    /**
     * ISO8601 representation of the moment this exception was thrown
     *
     * @var \DateTimeImmutable
     */
    protected \DateTimeImmutable $raisedAt;

    /**
     * @var array
     */
    protected array $links = [];

    /**
     * @param string $message
     * @param int $code
     * @param string|null $field
     * @param RequestInterface|null $request
     * @param ResponseInterface|null $response
     * @param Throwable|null $previous
     * @throws ApiException
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        ?string $field = null,
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
        ?Throwable $previous = null
    ) {
        $this->plainMessage = $message;

        $this->raisedAt = new \DateTimeImmutable();

        $formattedRaisedAt = $this->raisedAt->format(DateTime::ATOM);
        $message = "[{$formattedRaisedAt}] " . $message;

        if (! empty($field)) {
            $this->field = (string)$field;
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

    /**
     * @param ResponseInterface $response
     * @param ?RequestInterface $request
     * @param Throwable|null $previous
     * @return ApiException
     * @throws ApiException
     */
    public static function createFromResponse(ResponseInterface $response, ?RequestInterface $request = null, ?Throwable $previous = null): self
    {
        $object = static::parseResponseBody($response);

        $field = null;
        if (! empty($object->field)) {
            $field = $object->field;
        }

        return new self(
            "Error executing API call ({$object->status}: {$object->title}): {$object->detail}",
            $response->getStatusCode(),
            $field,
            $request,
            $response,
            $previous
        );
    }

    /**
     * @return string|null
     */
    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * @return string|null
     */
    public function getDocumentationUrl(): ?string
    {
        return $this->getUrl('documentation');
    }

    /**
     * @return string|null
     */
    public function getDashboardUrl(): ?string
    {
        return $this->getUrl('dashboard');
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasResponse(): bool
    {
        return ! ! $this->response;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasLink($key): bool
    {
        return array_key_exists($key, $this->links);
    }

    /**
     * @param string $key
     * @return \stdClass|null
     */
    public function getLink($key): ?\stdClass
    {
        if ($this->hasLink($key)) {
            return $this->links[$key];
        }

        return null;
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function getUrl($key): ?string
    {
        if ($this->hasLink($key)) {
            return $this->getLink($key)->href;
        }

        return null;
    }

    /**
     * @return null|RequestInterface
     */
    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    /**
     * Get the ISO8601 representation of the moment this exception was thrown
     *
     * @return \DateTimeImmutable
     */
    public function getRaisedAt(): \DateTimeImmutable
    {
        return $this->raisedAt;
    }

    /**
     * @param ResponseInterface $response
     * @return \stdClass
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
     *
     * @return string
     */
    public function getPlainMessage(): string
    {
        return $this->plainMessage;
    }
}
