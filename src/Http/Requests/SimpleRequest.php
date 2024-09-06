<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;

abstract class SimpleRequest extends Request
{
    protected string $id;

    protected bool $testmode;

    public function __construct(string $id, bool $testmode = false)
    {
        $this->id = $id;
        $this->testmode = $testmode;
    }

    protected function defaultQuery(): array
    {
        return [
            'testmode' => $this->testmode,
        ];
    }
}
