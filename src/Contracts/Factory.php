<?php

namespace Mollie\Api\Contracts;

interface Factory
{
    public static function new(array $data): static;

    /**
     * @return mixed
     */
    public function create();
}
