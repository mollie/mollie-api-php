<?php

namespace Mollie\Api\Exceptions;

use DateTime;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiException extends \Exception
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * ISO8601 representation of the moment this exception was thrown
     *
     * @var \DateTimeImmutable
     */
    protected $raisedAt;

    /**
     * @var array
     */
    protected $links = [];

    /**
     * @param string $message
     * @param int $code
     * @param string|null $field
     * @param RequestInterface|null $request
     * @param ResponseInterface|null $response
     * @param \Throwable|null $previous
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function __construct(
        $message = "",
        $code = 0,
        $field = null,
        RequestInterface $request = null,
        ResponseInterface $response = null,
        $previous = null
    ) {
        $this->raisedAt = new \DateTimeImmutable();

        $formattedRaisedAt = $this->raisedAt->format(DateTime::ISO8601);
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
     * @param \GuzzleHttp\Exception\GuzzleException $guzzleException
     * @param RequestInterface|null $request
     * @param \Throwable|null $previous
     * @return \Mollie\Api\Exceptions\ApiException
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public static function createFromGuzzleException(
        $guzzleException,
        $request = null,
        $previous = null
    ) {
        // Not all Guzzle Exceptions implement hasResponse() / getResponse()
        if (method_exists($guzzleException, 'hasResponse') && method_exists($guzzleException, 'getResponse')) {
            if ($guzzleException->hasResponse()) {
                return static::createFromResponse($guzzleException->getResponse(), $request, $previous);
            }
        }

        return new self($guzzleException->getMessage(), $guzzleException->getCode(), null, $request, null, $previous);
    }

    /**
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @param \Throwable|null $previous
     * @return \Mollie\Api\Exceptions\ApiException
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public static function createFromResponse(ResponseInterface $response, RequestInterface $request = null, $previous = null)
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
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string|null
     */
    public function getDocumentationUrl()
    {
        return $this->getUrl('documentation');
    }

    /**
     * @return string|null
     */
    public function getDashboardUrl()
    {
        return $this->getUrl('dashboard');
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response !== null;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasLink($key)
    {
        return array_key_exists($key, $this->links);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getLink($key)
    {
        if ($this->hasLink($key)) {
            return $this->links[$key];
        }

        return null;
    }

    /**
     * @param $key
     * @return null
     */
    public function getUrl($key)
    {
        if ($this->hasLink($key)) {
            return $this->getLink($key)->href;
        }

        return null;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the ISO8601 representation of the moment this exception was thrown
     *
     * @return \DateTimeImmutable
     */
    public function getRaisedAt()
    {
        return $this->raisedAt;
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    protected static function parseResponseBody($response)
    {
        $body = (string) $response->getBody();

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new self("Unable to decode Mollie response: '{$body}'.");
        }

        return $object;
    }
}
