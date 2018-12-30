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
     * @var Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $links = [];

    /**
     * @param string $message
     * @param int $code
     * @param string|null $field
     * @param \GuzzleHttp\Psr7\Response|null $response
     * @param \Throwable|null $previous
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function __construct(
        $message = "",
        $code = 0,
        $field = null,
        Response $response = null,
        Throwable $previous = null
    )
    {
        if (!empty($field)) {
            $this->field = (string)$field;
            $message .= ". Field: {$this->field}";
        }

        if (!empty($response)) {
            $this->response = $response;

            $object = static::parseResponseBody($this->response);

            if(isset($object->_links)) {
                foreach ($object->_links as $key => $value) {
                    $this->links[$key] = $value;
                }
            }
        }

        if ($this->hasLink('documentation')) {
            $message .= ". Documentation: {$this->getDocumentationUrl()}";
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

        return new static($guzzleException->getMessage(), $guzzleException->getCode(), null, $previous);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Throwable|null $previous
     * @return \Mollie\Api\Exceptions\ApiException
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public static function createFromResponse($response, Throwable $previous = null)
    {
        $object = static::parseResponseBody($response);

        $field = null;
        if (!empty($object->field)) {
            $field = $object->field;
        }

        return new static(
            "Error executing API call ({$object->status}: {$object->title}): {$object->detail}",
            $response->getStatusCode(),
            $field,
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
        if($this->hasLink($key)) {
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
        if($this->hasLink($key)) {
            return $this->getLink($key)->href;
        }
        return null;
    }

    /**
     * @param $response
     * @return mixed
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    protected static function parseResponseBody($response)
    {
        $body = (string) $response->getBody();

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new static("Unable to decode Mollie response: '{$body}'.");
        }

        return $object;
    }
}
