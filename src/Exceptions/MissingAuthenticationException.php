<?php

namespace Mollie\Api\Exceptions;

class MissingAuthenticationException extends MollieException
{
    public function __construct()
    {
        parent::__construct('You have not set an API key or OAuth access token. Please use setApiKey() to set the API key.');
    }
}
