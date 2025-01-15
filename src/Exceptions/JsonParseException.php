<?php

namespace Mollie\Api\Exceptions;

class JsonParseException extends MollieException
{
    private string $rawResponse;

    public function __construct(string $message, string $rawResponse)
    {
        parent::__construct($message);

        $this->rawResponse = $rawResponse;
    }

    public function getRawResponse(): string
    {
        return $this->rawResponse;
    }
}
