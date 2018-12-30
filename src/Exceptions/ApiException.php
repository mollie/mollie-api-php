<?php

namespace Mollie\Api\Exceptions;

use GuzzleHttp\Psr7\Response;
use Throwable;

class ApiException extends \Exception
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $documentationUrl;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param string $message
     * @param int $code
     * @param string|null $field
     * @param string|null $documentationUrl
     * @param \Throwable|null $previous
     * @param \GuzzleHttp\Psr7\Response|null $response
     */
    public function __construct(
        $message = "",
        $code = 0,
        $field = null,
        $documentationUrl = null,
        Throwable $previous = null,
        Response $response = null
    )
    {
        if (!empty($field)) {
            $this->field = (string)$field;
            $message .= ". Field: {$this->field}";
        }

        if (!empty($documentationUrl)) {
            $this->documentationUrl = (string)$documentationUrl;
            $message .= ". Documentation: {$this->documentationUrl}";
        }

        if (!empty($response)) {
            $this->response = $response;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @param \GuzzleHttp\Exception\RequestException $guzzleException
     * @param \Throwable $previous
     * @return \Mollie\Api\Exceptions\ApiException
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public static function createFromGuzzleException($guzzleException, Throwable $previous = null)
    {
        // Not all Guzzle Exceptions implement hasResponse() / getResponse()
        if(method_exists($guzzleException, 'hasResponse') && method_exists($guzzleException, 'getResponse')) {
            if($guzzleException->hasResponse()) {
                return static::createFromResponse($guzzleException->getResponse());
            }
        }

        return new static(
            $guzzleException->getMessage(),
            $guzzleException->getCode(),
            null,
            null,
            $previous
        );
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Throwable|null $previous
     * @return \Mollie\Api\Exceptions\ApiException
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public static function createFromResponse($response, Throwable $previous = null)
    {
        $body = (string) $response->getBody();

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new static("Unable to decode Mollie response: '{$body}'.");
        }

        $field = null;
        if (!empty($object->field)) {
            $field = $object->field;
        }

        $documentationUrl = null;
        if (!empty($object->_links) && !empty($object->_links->documentation)) {
            $documentationUrl = $object->_links->documentation->href;
        }

        return new static(
            "Error executing API call ({$object->status}: {$object->title}): {$object->detail}",
            $response->getStatusCode(),
            $field,
            $documentationUrl,
            $previous,
            $response
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
        return $this->documentationUrl;
    }

    /**
     * @return Response|null
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
}
