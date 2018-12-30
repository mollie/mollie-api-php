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
     */
    public static function createFromGuzzleException($guzzleException, \Throwable $previous = null)
    {
        $response = null;

        // Not all Guzzle Exceptions implement hasResponse() / getResponse()
        if(method_exists($guzzleException, 'hasResponse')) {
            if($guzzleException->hasResponse()) {
                $response = $guzzleException->getResponse();
            }
        }

        return new static(
            $guzzleException->getMessage(),
            $guzzleException->getCode(),
            null,
            null,
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
