<?php

namespace Mollie\Api\Exceptions;

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
     * @param string          $message
     * @param int             $code
     * @param string|null     $field
     * @param string|null     $documentationUrl
     * @param \Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, $field = null, $documentationUrl = null, \Throwable $previous = null)
    {
        if (!empty($field)) {
            $this->field = (string)$field;
            $message .= " Field: {$this->field}.";
        }

        if (!empty($documentationUrl)) {
            $this->documentationUrl = (string)$documentationUrl;
            $message .= " Documentation: {$this->documentationUrl}.";
        }

        parent::__construct($message, $code, $previous);


    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return $this->documentationUrl;
    }
}
