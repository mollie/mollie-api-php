<?php

declare(strict_types=1);

namespace Mollie\Api\Exceptions;

class JsonParseException extends MollieException
{
    public function __construct(
        string $message,
        public readonly string $rawResponse
    ) {
        parent::__construct($message);
    }

    public function getRawResponse(): string
    {
        return $this->rawResponse;
    }
}
