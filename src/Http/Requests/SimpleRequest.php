<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;

abstract class SimpleRequest extends Request
{
    protected string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
