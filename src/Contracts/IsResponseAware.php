<?php

namespace Mollie\Api\Contracts;

use Mollie\Api\Http\Response;

interface IsResponseAware extends ViableResponse
{
    public function getResponse(): ?Response;

    public function setResponse(?Response $response): self;
}