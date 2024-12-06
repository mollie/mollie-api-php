<?php

namespace Mollie\Api\Contracts;

interface Factory
{
    public static function new(array $data): self;

    /**
     * @return mixed
     */
    public function create();
}
