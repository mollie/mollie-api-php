<?php

namespace Mollie\Api\Contracts;

use Mollie\Api\Http\Response;
use Mollie\Api\Http\ViableResponse;

interface ResponseMiddleware
{
    /**
     * @return Response|ViableResponse|mixed|void
     */
    public function __invoke(Response $response);
}
