<?php

namespace Mollie\Api\Contracts;

use Mollie\Api\Http\Response;

interface HasResponse extends ViableResponse
{
    public function getResponse(): Response;
}
