<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

use Mollie\Api\Http\Response;

interface ResponseMiddleware
{
    /**
     * @return Response|void
     */
    public function __invoke(Response $response);
}
